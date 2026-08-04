<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';
import { badgeVariants, type BadgeVariants } from '.';

interface Props {
    class?: HTMLAttributes['class'];
    variant?: BadgeVariants['variant'];
}

const props = defineProps<Props>();
</script>

<template>
    <div :class="cn(badgeVariants({ variant: props.variant }), props.class)">
        <slot />
    </div>
</template>
