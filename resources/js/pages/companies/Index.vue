<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Building2, Plus, Pencil, Trash2 } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { create, destroy, edit, index } from '@/routes/companies';
import type { Company } from '@/types';

type Props = {
    companies: Company[];
};

defineProps<Props>();

const confirmDelete = (company: Company) => {
    if (!window.confirm(`Delete ${company.name}? This cannot be undone.`)) {
        return;
    }

    router.delete(destroy(company.id).url, {
        preserveScroll: true,
    });
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Companies',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Companies" />

    <h1 class="sr-only">Companies</h1>

    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                title="Companies"
                description="Manage your company records and branding"
            />

            <Button as-child data-test="companies-new-button">
                <Link :href="create()">
                    <Plus class="mr-2 h-4 w-4" />
                    New company
                </Link>
            </Button>
        </div>

        <div class="grid gap-4">
            <div
                v-for="company in companies"
                :key="company.id"
                class="flex flex-col gap-4 rounded-xl border bg-background p-4 md:flex-row md:items-center md:justify-between"
            >
                <div class="flex items-center gap-4">
                    <Avatar class="h-14 w-14 rounded-lg">
                        <AvatarImage
                            v-if="company.logoUrl"
                            :src="company.logoUrl"
                            :alt="company.name"
                        />
                        <AvatarFallback class="rounded-lg">
                            <Building2 class="h-5 w-5" />
                        </AvatarFallback>
                    </Avatar>

                    <div class="space-y-1">
                        <div class="font-medium">
                            {{ company.name }}
                        </div>
                        <div class="text-sm text-muted-foreground">
                            <span v-if="company.email">{{ company.email }}</span>
                            <span v-if="company.email && company.website"> · </span>
                            <a
                                v-if="company.website"
                                class="text-foreground underline-offset-4 hover:underline"
                                :href="company.website"
                                target="_blank"
                                rel="noreferrer"
                            >
                                {{ company.website }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="edit(company.id)">
                            <Pencil class="mr-2 h-4 w-4" />
                            Edit
                        </Link>
                    </Button>
                    <Button
                        variant="destructive"
                        size="sm"
                        @click="confirmDelete(company)"
                    >
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete
                    </Button>
                </div>
            </div>

            <p
                v-if="companies.length === 0"
                class="py-10 text-center text-muted-foreground"
            >
                No companies have been added yet.
            </p>
        </div>
    </div>
</template>
