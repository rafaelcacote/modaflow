<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import type { BreadcrumbItem } from '@/types';

const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Empresas', href: '/empresas' },
    { title: 'Nova Empresa', href: '/empresas/create' },
];

const form = useForm({
    razao_social: '',
    nome_fantasia: '',
    cnpj: '',
    email: '',
    telefone: '',
    ativo: true,
    data_adesao: new Date().toISOString().split('T')[0],
    data_expiracao: '',
});

const submit = () => {
    form.post('/empresas', {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Empresa cadastrada com sucesso!');
        },
        onError: () => {
            toast.error('Erro ao cadastrar empresa. Verifique os campos.');
        },
    });
};

const formatCNPJ = (value: string) => {
    // Remove tudo que não é dígito
    const digits = value.replace(/\D/g, '');
    
    // Formata no padrão XX.XXX.XXX/XXXX-XX
    if (digits.length <= 2) return digits;
    if (digits.length <= 5) return `${digits.slice(0, 2)}.${digits.slice(2)}`;
    if (digits.length <= 8) return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5)}`;
    if (digits.length <= 12) return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8)}`;
    return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8, 12)}-${digits.slice(12, 14)}`;
};

const handleCNPJInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    form.cnpj = formatCNPJ(input.value);
};

const formatPhone = (value: string) => {
    // Remove tudo que não é dígito
    const digits = value.replace(/\D/g, '');
    
    // Formata no padrão (XX) XXXXX-XXXX ou (XX) XXXX-XXXX
    if (digits.length <= 2) return digits;
    if (digits.length <= 6) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
    if (digits.length <= 10) return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
    return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7, 11)}`;
};

const handlePhoneInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    form.telefone = formatPhone(input.value);
};
</script>

<template>
    <Head title="Nova Empresa" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl space-y-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold">Nova Empresa</h1>
                <p class="text-muted-foreground">
                    Cadastre uma nova empresa no sistema
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Dados da Empresa</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Razão Social -->
                        <div class="space-y-2">
                            <Label for="razao_social" class="required">
                                Razão Social
                            </Label>
                            <Input
                                id="razao_social"
                                v-model="form.razao_social"
                                required
                                placeholder="Ex: Empresa LTDA"
                                :disabled="form.processing"
                            />
                            <InputError :message="form.errors.razao_social" />
                        </div>

                        <!-- Nome Fantasia -->
                        <div class="space-y-2">
                            <Label for="nome_fantasia" class="required">
                                Nome Fantasia
                            </Label>
                            <Input
                                id="nome_fantasia"
                                v-model="form.nome_fantasia"
                                required
                                placeholder="Ex: Empresa"
                                :disabled="form.processing"
                            />
                            <InputError :message="form.errors.nome_fantasia" />
                        </div>

                        <!-- CNPJ e Email -->
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="cnpj">CNPJ</Label>
                                <Input
                                    id="cnpj"
                                    v-model="form.cnpj"
                                    @input="handleCNPJInput"
                                    placeholder="00.000.000/0000-00"
                                    maxlength="18"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.cnpj" />
                            </div>

                            <div class="space-y-2">
                                <Label for="email" class="required">Email</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    placeholder="empresa@example.com"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.email" />
                            </div>
                        </div>

                        <!-- Telefone -->
                        <div class="space-y-2">
                            <Label for="telefone">Telefone</Label>
                            <Input
                                id="telefone"
                                v-model="form.telefone"
                                @input="handlePhoneInput"
                                placeholder="(00) 00000-0000"
                                maxlength="15"
                                :disabled="form.processing"
                            />
                            <InputError :message="form.errors.telefone" />
                        </div>

                        <!-- Datas -->
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="data_adesao">Data de Adesão</Label>
                                <Input
                                    id="data_adesao"
                                    v-model="form.data_adesao"
                                    type="date"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.data_adesao" />
                            </div>

                            <div class="space-y-2">
                                <Label for="data_expiracao">Data de Expiração</Label>
                                <Input
                                    id="data_expiracao"
                                    v-model="form.data_expiracao"
                                    type="date"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.data_expiracao" />
                            </div>
                        </div>

                        <!-- Ativo -->
                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="ativo"
                                :checked="form.ativo"
                                @update:checked="(value) => (form.ativo = value as boolean)"
                                :disabled="form.processing"
                            />
                            <Label for="ativo" class="cursor-pointer">
                                Empresa Ativa
                            </Label>
                        </div>
                    </CardContent>
                </Card>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4">
                    <Button
                        type="button"
                        variant="outline"
                        @click="$inertia.visit('/empresas')"
                        :disabled="form.processing"
                    >
                        Cancelar
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Salvando...' : 'Salvar Empresa' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.required::after {
    content: ' *';
    color: hsl(var(--destructive));
}
</style>

