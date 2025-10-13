<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Plus, Search, Pencil, Trash2 } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';
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

interface PaginatedEmpresas {
    data: Empresa[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

interface Props {
    empresas: PaginatedEmpresas;
    filters: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Empresas', href: '/empresas' },
];

// Filters
const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');

// Delete dialog
const showDeleteDialog = ref(false);
const empresaToDelete = ref<Empresa | null>(null);
const isDeleting = ref(false);

// Watch filters and update URL
watch([search, status], () => {
    router.get(
        '/empresas',
        {
            search: search.value || undefined,
            status: status.value !== 'all' ? status.value : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}, { debounce: 300 });

// Show toast on success message
if (page.props.flash?.success) {
    toast.success(page.props.flash.success as string);
}

const openDeleteDialog = (empresa: Empresa) => {
    empresaToDelete.value = empresa;
    showDeleteDialog.value = true;
};

const closeDeleteDialog = () => {
    empresaToDelete.value = null;
    showDeleteDialog.value = false;
};

const deleteEmpresa = () => {
    if (!empresaToDelete.value) return;

    isDeleting.value = true;

    router.delete(`/empresas/${empresaToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteDialog();
            toast.success('Empresa excluída com sucesso!');
        },
        onError: () => {
            toast.error('Erro ao excluir empresa');
        },
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

const formatCNPJ = (cnpj: string | null) => {
    if (!cnpj) return '-';
    return cnpj;
};
</script>

<template>
    <Head title="Empresas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Empresas</h1>
                    <p class="text-muted-foreground">
                        Gerencie as empresas cadastradas no sistema
                    </p>
                </div>
                <Link href="/empresas/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nova Empresa
                    </Button>
                </Link>
            </div>

            <!-- Filters -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center">
                <div class="relative flex-1">
                    <Search
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="search"
                        placeholder="Buscar por razão social, nome fantasia ou CNPJ..."
                        class="pl-10"
                    />
                </div>

                <select
                    v-model="status"
                    class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:w-[200px]"
                >
                    <option value="all">Todos</option>
                    <option value="ativo">Ativos</option>
                    <option value="inativo">Inativos</option>
                </select>
            </div>

            <!-- Table -->
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Razão Social</TableHead>
                            <TableHead>Nome Fantasia</TableHead>
                            <TableHead>CNPJ</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Data Adesão</TableHead>
                            <TableHead class="text-right">Ações</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-if="empresas.data.length === 0"
                            class="hover:bg-transparent"
                        >
                            <TableCell colspan="7" class="text-center">
                                <div class="py-8 text-muted-foreground">
                                    Nenhuma empresa encontrada
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="empresa in empresas.data" :key="empresa.id">
                            <TableCell class="font-medium">
                                {{ empresa.razao_social }}
                            </TableCell>
                            <TableCell>{{ empresa.nome_fantasia }}</TableCell>
                            <TableCell>{{ formatCNPJ(empresa.cnpj) }}</TableCell>
                            <TableCell>{{ empresa.email }}</TableCell>
                            <TableCell>
                                <Badge
                                    :variant="empresa.ativo ? 'default' : 'secondary'"
                                >
                                    {{ empresa.ativo ? 'Ativo' : 'Inativo' }}
                                </Badge>
                            </TableCell>
                            <TableCell>{{ formatDate(empresa.data_adesao) }}</TableCell>
                            <TableCell class="text-right">
                                <div class="flex justify-end gap-2">
                                    <Link :href="`/empresas/${empresa.id}/edit`">
                                        <Button variant="ghost" size="sm">
                                            <Pencil class="h-4 w-4" />
                                            <span class="sr-only">Editar</span>
                                        </Button>
                                    </Link>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        @click="openDeleteDialog(empresa)"
                                    >
                                        <Trash2 class="h-4 w-4 text-red-500" />
                                        <span class="sr-only">Excluir</span>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div
                v-if="empresas.last_page > 1"
                class="flex items-center justify-between"
            >
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ empresas.data.length }} de {{ empresas.total }} empresas
                </div>
                <div class="flex gap-2">
                    <Link
                        v-for="link in empresas.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'px-3 py-1 rounded border text-sm',
                            link.active
                                ? 'bg-primary text-primary-foreground'
                                : 'bg-background hover:bg-muted',
                            !link.url && 'opacity-50 cursor-not-allowed',
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirmar Exclusão</DialogTitle>
                    <DialogDescription>
                        Tem certeza que deseja excluir a empresa
                        <strong>{{ empresaToDelete?.razao_social }}</strong>?
                        <br />
                        Esta ação não pode ser desfeita.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button
                        variant="outline"
                        @click="closeDeleteDialog"
                        :disabled="isDeleting"
                    >
                        Cancelar
                    </Button>
                    <Button
                        variant="destructive"
                        @click="deleteEmpresa"
                        :disabled="isDeleting"
                    >
                        {{ isDeleting ? 'Excluindo...' : 'Excluir' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

