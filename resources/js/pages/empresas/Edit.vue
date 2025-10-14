<script setup lang="ts">
import EmpresaController from '@/actions/App/Http/Controllers/EmpresaController';
import { index as empresasIndex } from '@/routes/empresas';
import { Form, Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import DeleteEmpresa from '@/components/DeleteEmpresa.vue';
import { useToast } from '@/composables/useToast';
import EmpresaForm from '@/components/empresas/EmpresaForm.vue';
import FormActions from '@/components/empresas/FormActions.vue';
import type { BreadcrumbItem } from '@/types';

interface Empresa {
    id: number;
    uuid: string;
    razao_social: string;
    nome_fantasia: string;
    cnpj: string | null;
    email: string;
    telefone: string | null;
    ativo: boolean;
    data_adesao: string;
    data_expiracao: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    empresa: Empresa;
}

const props = defineProps<Props>();
const page = usePage();
const toast = useToast();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Empresas', href: empresasIndex().url },
    { title: props.empresa.razao_social, href: EmpresaController.edit(props.empresa).url },
];
</script>

<template>
    <Head :title="`Editar ${empresa.razao_social}`" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Page Header -->
            <div class="space-y-2">
                <h1 class="text-3xl font-bold tracking-tight text-foreground">
                    Editar Empresa
                </h1>
                <p class="text-base text-muted-foreground">
                    Atualize as informações da empresa {{ empresa.nome_fantasia }}.
                </p>
            </div>

            <!-- Info Card -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">UUID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ empresa.uuid }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(empresa.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="EmpresaController.update.form(empresa)"
                enctype="multipart/form-data"
                :options="{
                    preserveScroll: true,
                }"
                @success="toast.success('Empresa atualizada com sucesso!')"
                @error="toast.error('Erro ao atualizar empresa', 'Verifique os campos e tente novamente.')"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <EmpresaForm 
                    :empresa="empresa"
                    :errors="errors"
                    :processing="processing"
                />
                
                <FormActions 
                    :processing="processing"
                    :recentlySuccessful="recentlySuccessful"
                />
            </Form>
        </div>
    </AppLayout>
</template>


