<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Loader2 } from 'lucide-vue-next';

interface Props {
    processing: boolean;
    isCreate: boolean;
    empresaId: number;
}

defineProps<Props>();
</script>

<template>
    <Card class="border-border shadow-sm">
        <CardContent class="flex justify-end gap-4 pt-6">
            <Link :href="`/empresas/${empresaId}/lojas`">
                <Button 
                    variant="outline" 
                    type="button" 
                    :disabled="processing"
                >
                    Cancelar
                </Button>
            </Link>
            <Button 
                type="submit" 
                :disabled="processing"
                class="min-w-[120px]"
            >
                <template v-if="processing">
                    <Loader2 class="mr-2 h-4 w-4 animate-spin" />
                    Salvando...
                </template>
                <template v-else>
                    {{ isCreate ? 'Cadastrar' : 'Atualizar' }}
                </template>
            </Button>
        </CardContent>
    </Card>
</template>

