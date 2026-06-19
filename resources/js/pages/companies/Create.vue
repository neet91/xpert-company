<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ImagePlus } from '@lucide/vue';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index, store } from '@/routes/companies';

const form = useForm({
    name: '',
    email: '',
    website: '',
    logo: null as File | null,
});

const submit = () => {
    form.post(store().url, {
        forceFormData: true,
        preserveScroll: true,
    });
};

const logoName = computed(() => form.logo?.name ?? '');

const handleLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.logo = target.files?.[0] ?? null;
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Companies',
                href: index(),
            },
            {
                title: 'Create',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Create Company" />

    <h1 class="sr-only">Create Company</h1>

    <div class="max-w-2xl space-y-6">
        <Heading
            variant="small"
            title="Create company"
            description="Add your company name, contact details, and logo"
        />

        <form class="space-y-6" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    required
                    autocomplete="organization"
                    data-test="company-name-input"
                />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    data-test="company-email-input"
                />
                <InputError :message="form.errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="website">Website</Label>
                <Input
                    id="website"
                    v-model="form.website"
                    type="url"
                    placeholder="https://example.com"
                    data-test="company-website-input"
                />
                <InputError :message="form.errors.website" />
            </div>

            <div class="grid gap-2">
                <Label for="logo">Logo</Label>
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-20 w-20 items-center justify-center rounded-xl border border-dashed bg-muted/30 text-muted-foreground"
                    >
                        <ImagePlus class="h-6 w-6" />
                    </div>
                    <div class="space-y-2">
                        <Input
                            id="logo"
                            type="file"
                            accept="image/*"
                            data-test="company-logo-input"
                            @change="handleLogoChange"
                        />
                        <p
                            v-if="logoName"
                            class="text-sm text-muted-foreground"
                        >
                            Selected: {{ logoName }}
                        </p>
                    </div>
                </div>
                <InputError :message="form.errors.logo" />
            </div>

            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="form.processing">
                    Create company
                </Button>
                <Button variant="ghost" as-child>
                    <Link :href="index()">Cancel</Link>
                </Button>
            </div>
        </form>
    </div>
</template>
