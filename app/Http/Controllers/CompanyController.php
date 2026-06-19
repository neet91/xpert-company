<?php

namespace App\Http\Controllers;

use App\Http\Requests\Companies\SaveCompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies.
     */
    public function index(): Response
    {
        return Inertia::render('companies/Index', [
            'companies' => Company::query()
                ->latest()
                ->get()
                ->map(fn (Company $company) => $this->toArray($company)),
        ]);
    }

    /**
     * Show the form for creating a new company.
     */
    public function create(): Response
    {
        return Inertia::render('companies/Create');
    }

    /**
     * Store a newly created company.
     */
    public function store(SaveCompanyRequest $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        if ($logo = $request->file('logo')) {
            $data['logo'] = $logo->store('companies', 'public');
        }

        $company = Company::create($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Company created.')]);

        return to_route('companies.edit', $company);
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company): Response
    {
        return Inertia::render('companies/Edit', [
            'company' => $this->toArray($company),
        ]);
    }

    /**
     * Update the specified company.
     */
    public function update(SaveCompanyRequest $request, Company $company): RedirectResponse
    {
        $data = $this->validatedData($request);
        $oldLogo = $company->logo;

        if ($logo = $request->file('logo')) {
            $data['logo'] = $logo->store('companies', 'public');
        }

        $company->update($data);

        if (isset($data['logo']) && $oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Company updated.')]);

        return to_route('companies.edit', $company);
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Company $company): RedirectResponse
    {
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Company deleted.')]);

        return to_route('companies.index');
    }

    /**
     * Normalize validated request data.
     *
     * @return array{name: string, email: string|null, website: string|null}
     */
    private function validatedData(SaveCompanyRequest $request): array
    {
        $validated = $request->validated();

        return [
            'name' => $validated['name'],
            'email' => filled($validated['email'] ?? null) ? $validated['email'] : null,
            'website' => filled($validated['website'] ?? null) ? $validated['website'] : null,
        ];
    }

    /**
     * Convert a company model into an array for Inertia.
     *
     * @return array{id: int, name: string, email: string|null, website: string|null, logo: string|null, logoUrl: string|null, createdAt: string, updatedAt: string}
     */
    private function toArray(Company $company): array
    {
        return [
            'id' => $company->id,
            'name' => $company->name,
            'email' => $company->email,
            'website' => $company->website,
            'logo' => $company->logo,
            'logoUrl' => $company->logo ? Storage::disk('public')->url($company->logo) : null,
            'createdAt' => $company->created_at?->toISOString(),
            'updatedAt' => $company->updated_at?->toISOString(),
        ];
    }
}
