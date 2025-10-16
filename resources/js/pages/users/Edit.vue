<script setup lang="ts">
import UserController from '@/actions/App/Http/Controllers/UserController';
import { index as usersIndex } from '@/routes/users';
import { Form, Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { useToast } from '@/composables/useToast';
import UserForm from '@/components/users/UserForm.vue';
import FormActions from '@/components/users/FormActions.vue';
import { User as UserIcon } from 'lucide-vue-next';
import type { BreadcrumbItem, User as UserType, Empresa } from '@/types';

interface Props {
    user: UserType;
    empresas: Empresa[];
}

const props = defineProps<Props>();
const toast = useToast();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Usuários', href: usersIndex().url },
    { title: props.user.name, href: UserController.edit(props.user).url },
];
</script>

<template>
    <Head :title="`Editar ${user.name}`" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Page Header -->
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <UserIcon class="h-8 w-8 text-primary" />
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">
                        Editar Usuário
                    </h1>
                </div>
                <p class="text-base text-muted-foreground">
                    Atualize as informações do usuário {{ user.name }}.
                </p>
            </div>

            <!-- Info Card (UUID, Data de Cadastro) -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="font-medium text-muted-foreground">ID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ user.id }}</span>
                        </div>
                        <div class="sm:text-right">
                            <span class="font-medium text-muted-foreground">Cadastrado em:</span>
                            <span class="ml-2 text-foreground">
                                {{ new Date(user.created_at).toLocaleDateString('pt-BR') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Form -->
            <Form
                v-bind="UserController.update.form(user)"
                :options="{ preserveScroll: true }"
                @success="toast.success('Usuário atualizado com sucesso!')"
                @error="toast.error('Erro ao atualizar usuário')"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <UserForm 
                    :user="user"
                    :errors="errors"
                    :processing="processing"
                    :empresas="empresas"
                />
                
                <FormActions 
                    :processing="processing"
                    :recentlySuccessful="recentlySuccessful"
                />
            </Form>
        </div>
    </AppLayout>
</template>
