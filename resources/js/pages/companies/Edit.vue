<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ImagePlus, Trash2 } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { destroy, edit, index, update } from '@/routes/companies';
import type { Company } from '@/types';

type Props = {
    company: Company;
};

const props = defineProps<Props>();

const form = useForm({
    name: props.company.name,
    email: props.company.email ?? '',
    website: props.company.website ?? '',
    logo: null as File | null,
});

const submit = () => {
    form.patch(update(props.company.id).url, {
        forceFormData: true,
        preserveScroll: true,
    });
};

const logoName = computed(() => form.logo?.name ?? '');
const logoPreview = ref<string | null>(props.company.logoUrl);

watch(
    () => form.logo,
    (logo, _, onCleanup) => {
        if (!logo) {
            logoPreview.value = props.company.logoUrl;
            return;
        }

        const preview = URL.createObjectURL(logo);
        logoPreview.value = preview;

        onCleanup(() => URL.revokeObjectURL(preview));
    },
    { immediate: true },
);

const handleLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.logo = target.files?.[0] ?? null;
};

const removeCompany = () => {
    if (!window.confirm(`Delete ${props.company.name}? This cannot be undone.`)) {
        return;
    }

    form.delete(destroy(props.company.id).url, {
        preserveScroll: true,
    });
};

defineOptions({
    layout: (props: { company: Company }) => ({
        breadcrumbs: [
            {
                title: 'Companies',
                href: index(),
            },
            {
                title: props.company.name,
                href: edit(props.company.id),
            },
        ],
    }),
});
</script>

<template>
    <Head :title="`Edit ${company.name}`" />

    <h1 class="sr-only">Edit {{ company.name }}</h1>

    <div class="space-y-8">
        <div class="max-w-2xl space-y-6">
            <Heading
                variant="small"
                :title="`Edit ${company.name}`"
                description="Update the company profile and logo"
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
                        <Avatar class="h-20 w-20 rounded-xl">
                            <AvatarImage
                                v-if="logoPreview"
                                :src="logoPreview"
                                :alt="company.name"
                            />
                            <AvatarFallback class="rounded-xl">
                                <ImagePlus class="h-6 w-6" />
                            </AvatarFallback>
                        </Avatar>
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
                        Save changes
                    </Button>
                    <Button variant="ghost" as-child>
                        <Link :href="index()">Back</Link>
                    </Button>
                </div>
            </form>
        </div>

        <div
            class="max-w-2xl rounded-xl border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10"
        >
            <div class="space-y-2 text-red-600 dark:text-red-100">
                <p class="font-medium">Danger zone</p>
                <p class="text-sm">
                    Deleting a company permanently removes its record and logo.
                </p>
            </div>

            <Button
                class="mt-4"
                variant="destructive"
                @click="removeCompany"
            >
                <Trash2 class="mr-2 h-4 w-4" />
                Delete company
            </Button>
        </div>
    </div>
</template>
