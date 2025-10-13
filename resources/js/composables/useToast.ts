import { ref } from 'vue';

export interface Toast {
    id: string;
    type: 'success' | 'error' | 'info' | 'warning';
    title: string;
    message?: string;
    duration?: number;
}

const toasts = ref<Toast[]>([]);

let idCounter = 0;

export const useToast = () => {
    const add = (toast: Omit<Toast, 'id'>) => {
        const id = `toast-${++idCounter}`;
        const newToast: Toast = {
            id,
            duration: 5000,
            ...toast,
        };

        toasts.value.push(newToast);

        // Auto remove after duration
        if (newToast.duration && newToast.duration > 0) {
            setTimeout(() => {
                remove(id);
            }, newToast.duration);
        }

        return id;
    };

    const remove = (id: string) => {
        const index = toasts.value.findIndex((t) => t.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    };

    const success = (title: string, message?: string, duration?: number) => {
        return add({ type: 'success', title, message, duration });
    };

    const error = (title: string, message?: string, duration?: number) => {
        return add({ type: 'error', title, message, duration });
    };

    const info = (title: string, message?: string, duration?: number) => {
        return add({ type: 'info', title, message, duration });
    };

    const warning = (title: string, message?: string, duration?: number) => {
        return add({ type: 'warning', title, message, duration });
    };

    const clear = () => {
        toasts.value = [];
    };

    return {
        toasts,
        add,
        remove,
        success,
        error,
        info,
        warning,
        clear,
    };
};

