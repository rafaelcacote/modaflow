<script setup lang="ts">
import EmpresaController from '@/actions/App/Http/Controllers/EmpresaController';
import { index as empresasIndex } from '@/routes/empresas';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Pencil, ArrowLeft, MapPin, Phone, Mail, Calendar, Building2, FileText, Store, Plus } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface Endereco {
    id: number;
    endereco: string;
    numero: string | null;
    complemento: string | null;
    bairro: string | null;
    municipio_id: number;
    cep: string | null;
    referencia: string | null;
}

interface Loja {
    id: number;
    empresa_id: number;
    uuid: string;
    nome: string;
    cnpj: string | null;
    telefone: string | null;
    email: string | null;
    cep: string | null;
    logradouro: string | null;
    numero: string | null;
    complemento: string | null;
    bairro: string | null;
    cidade: string | null;
    estado: string | null;
    ativo: boolean;
    created_at: string;
    updated_at: string;
}

interface Empresa {
    id: number;
    uuid: string;
    razao_social: string;
    nome_fantasia: string;
    cnpj: string | null;
    email: string;
    logo_path: string | null;
    logo_url: string | null;
    telefone: string | null;
    ativo: boolean;
    data_adesao: string;
    data_expiracao: string | null;
    endereco?: Endereco | null;
    lojas?: Loja[];
    created_at: string;
    updated_at: string;
}

interface Props {
    empresa: Empresa;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Empresas', href: empresasIndex().url },
    { title: props.empresa.razao_social, href: EmpresaController.show(props.empresa).url },
];

const formatDate = (date: string | null) => {
    if (!date) return 'Não definido';
    return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};

const formatCNPJ = (cnpj: string | null) => {
    if (!cnpj) return 'Não informado';
    return cnpj;
};

const formatEndereco = (endereco: Endereco | null | undefined) => {
    if (!endereco) return 'Não cadastrado';
    
    let enderecoCompleto = endereco.endereco || '';
    if (endereco.numero) enderecoCompleto += `, ${endereco.numero}`;
    if (endereco.complemento) enderecoCompleto += ` - ${endereco.complemento}`;
    if (endereco.bairro) enderecoCompleto += ` - ${endereco.bairro}`;
    if (endereco.cep) enderecoCompleto += ` - CEP: ${endereco.cep}`;
    
    return enderecoCompleto || 'Endereço incompleto';
};

const formatEnderecoLoja = (loja: Loja) => {
    let enderecoCompleto = '';
    
    if (loja.logradouro) {
        enderecoCompleto = loja.logradouro;
        if (loja.numero) enderecoCompleto += `, ${loja.numero}`;
        if (loja.complemento) enderecoCompleto += ` - ${loja.complemento}`;
        if (loja.bairro) enderecoCompleto += ` - ${loja.bairro}`;
        if (loja.cidade) enderecoCompleto += ` - ${loja.cidade}`;
        if (loja.estado) enderecoCompleto += `/${loja.estado}`;
        if (loja.cep) enderecoCompleto += ` - CEP: ${loja.cep}`;
    }
    
    return enderecoCompleto || 'Endereço não cadastrado';
};
</script>

<template>
    <Head :title="`${empresa.razao_social}`" />

    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full max-w-[1920px] space-y-8 px-6 py-8 lg:px-8">
            <!-- Page Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <Link :href="empresasIndex().url">
                            <Button variant="ghost" size="sm" class="gap-2">
                                <ArrowLeft class="h-4 w-4" />
                                Voltar
                            </Button>
                        </Link>
                    </div>
                    <div class="flex items-center gap-4">
                        <div v-if="empresa.logo_url" class="w-16 h-16 rounded-lg overflow-hidden flex items-center justify-center bg-gray-50 border-2">
                            <img 
                                :src="empresa.logo_url" 
                                :alt="`Logo ${empresa.nome_fantasia}`"
                                class="max-w-full max-h-full object-contain"
                            />
                        </div>
                        <div v-else class="w-16 h-16 rounded-lg flex items-center justify-center bg-primary/10">
                            <Building2 class="h-8 w-8 text-primary" />
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight text-foreground">
                                {{ empresa.nome_fantasia }}
                            </h1>
                            <p class="text-base text-muted-foreground">
                                {{ empresa.razao_social }}
                            </p>
                        </div>
                    </div>
                </div>
                <Link :href="EmpresaController.edit(empresa).url">
                    <Button class="w-full sm:w-auto gap-2">
                        <Pencil class="h-4 w-4" />
                        Editar Empresa
                    </Button>
                </Link>
            </div>

            <!-- Status and UUID Card -->
            <Card class="border-border shadow-sm">
                <CardContent class="pt-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <div>
                                <span class="text-sm font-medium text-muted-foreground">Status:</span>
                                <Badge
                                    :variant="empresa.ativo ? 'default' : 'secondary'"
                                    class="ml-2"
                                >
                                    {{ empresa.ativo ? 'Ativa' : 'Inativa' }}
                                </Badge>
                            </div>
                        </div>
                        <div class="text-sm">
                            <span class="font-medium text-muted-foreground">UUID:</span>
                            <span class="ml-2 font-mono text-foreground">{{ empresa.uuid }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Informações da Empresa -->
                <Card class="border-border shadow-sm">
                    <CardHeader class="space-y-1">
                        <CardTitle class="flex items-center gap-2">
                            <Building2 class="h-5 w-5" />
                            Informações da Empresa
                        </CardTitle>
                        <CardDescription>
                            Dados cadastrais e de contato
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <FileText class="h-5 w-5 text-muted-foreground mt-0.5" />
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-muted-foreground">CNPJ</p>
                                    <p class="text-base text-foreground">{{ formatCNPJ(empresa.cnpj) }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <Mail class="h-5 w-5 text-muted-foreground mt-0.5" />
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-muted-foreground">Email</p>
                                    <p class="text-base text-foreground">{{ empresa.email }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <Phone class="h-5 w-5 text-muted-foreground mt-0.5" />
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-muted-foreground">Telefone</p>
                                    <p class="text-base text-foreground">{{ empresa.telefone || 'Não informado' }}</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Endereço -->
                <Card class="border-border shadow-sm">
                    <CardHeader class="space-y-1">
                        <CardTitle class="flex items-center gap-2">
                            <MapPin class="h-5 w-5" />
                            Endereço
                        </CardTitle>
                        <CardDescription>
                            Localização da empresa
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="empresa.endereco" class="space-y-3">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Endereço Completo</p>
                                <p class="text-base text-foreground">{{ formatEndereco(empresa.endereco) }}</p>
                            </div>
                            <div v-if="empresa.endereco.referencia" class="pt-2 border-t">
                                <p class="text-sm font-medium text-muted-foreground">Referência</p>
                                <p class="text-base text-foreground">{{ empresa.endereco.referencia }}</p>
                            </div>
                        </div>
                        <div v-else class="text-center py-6">
                            <MapPin class="h-12 w-12 text-muted-foreground mx-auto mb-2 opacity-50" />
                            <p class="text-sm text-muted-foreground">Nenhum endereço cadastrado</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Datas -->
            <Card class="border-border shadow-sm">
                <CardHeader class="space-y-1">
                    <CardTitle class="flex items-center gap-2">
                        <Calendar class="h-5 w-5" />
                        Datas Importantes
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Data de Adesão</p>
                            <p class="text-base text-foreground">{{ formatDate(empresa.data_adesao) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Data de Expiração</p>
                            <p 
                                class="text-base"
                                :class="{
                                    'text-red-600 font-medium': empresa.data_expiracao && new Date(empresa.data_expiracao) < new Date(),
                                    'text-foreground': !empresa.data_expiracao || new Date(empresa.data_expiracao) >= new Date()
                                }"
                            >
                                {{ formatDate(empresa.data_expiracao) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Cadastrado em</p>
                            <p class="text-base text-foreground">{{ formatDate(empresa.created_at) }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Lojas da Empresa -->
            <Card class="border-border shadow-sm">
                <CardHeader class="space-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle class="flex items-center gap-2">
                                <Store class="h-5 w-5" />
                                Lojas da Empresa
                            </CardTitle>
                            <CardDescription>
                                Lista de todas as lojas cadastradas para esta empresa
                            </CardDescription>
                        </div>
                        <Link :href="`/empresas/${empresa.id}/lojas`">
                            <Button variant="outline" size="sm" class="gap-2">
                                <Plus class="h-4 w-4" />
                                Nova Loja
                            </Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="empresa.lojas && empresa.lojas.length > 0" class="space-y-4">
                        <div 
                            v-for="loja in empresa.lojas" 
                            :key="loja.id"
                            class="border rounded-lg p-4 hover:bg-muted/50 transition-colors"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-foreground">{{ loja.nome }}</h3>
                                        <Badge
                                            :variant="loja.ativo ? 'default' : 'secondary'"
                                            class="text-xs"
                                        >
                                            {{ loja.ativo ? 'Ativa' : 'Inativa' }}
                                        </Badge>
                                    </div>
                                    
                                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                        <div v-if="loja.cnpj" class="flex items-center gap-2">
                                            <FileText class="h-4 w-4 text-muted-foreground" />
                                            <span class="text-sm text-muted-foreground">CNPJ:</span>
                                            <span class="text-sm text-foreground">{{ loja.cnpj }}</span>
                                        </div>
                                        
                                        <div v-if="loja.email" class="flex items-center gap-2">
                                            <Mail class="h-4 w-4 text-muted-foreground" />
                                            <span class="text-sm text-muted-foreground">Email:</span>
                                            <span class="text-sm text-foreground">{{ loja.email }}</span>
                                        </div>
                                        
                                        <div v-if="loja.telefone" class="flex items-center gap-2">
                                            <Phone class="h-4 w-4 text-muted-foreground" />
                                            <span class="text-sm text-muted-foreground">Telefone:</span>
                                            <span class="text-sm text-foreground">{{ loja.telefone }}</span>
                                        </div>
                                    </div>
                                    
                                    <div v-if="formatEnderecoLoja(loja) !== 'Endereço não cadastrado'" class="mt-3 flex items-start gap-2">
                                        <MapPin class="h-4 w-4 text-muted-foreground mt-0.5" />
                                        <div>
                                            <span class="text-sm text-muted-foreground">Endereço:</span>
                                            <p class="text-sm text-foreground">{{ formatEnderecoLoja(loja) }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2 ml-4">
                                    <Link :href="`/empresas/${empresa.id}/lojas/${loja.id}`">
                                        <Button variant="ghost" size="sm">
                                            Ver Detalhes
                                        </Button>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-8">
                        <Store class="h-12 w-12 text-muted-foreground mx-auto mb-4 opacity-50" />
                        <h3 class="text-lg font-medium text-muted-foreground mb-2">Nenhuma loja cadastrada</h3>
                        <p class="text-sm text-muted-foreground mb-4">
                            Esta empresa ainda não possui lojas cadastradas.
                        </p>
                        <Link :href="`/empresas/${empresa.id}/lojas/create`">
                            <Button class="gap-2">
                                <Plus class="h-4 w-4" />
                                Cadastrar Primeira Loja
                            </Button>
                        </Link>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

