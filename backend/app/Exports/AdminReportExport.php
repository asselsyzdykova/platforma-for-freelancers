<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Subscription;
use App\Models\Ticket;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AdminReportExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new SubscriptionsSheet(),
            new TicketsSheet(),
        ];
    }
}
class SubscriptionsSheet implements FromCollection, WithHeadings
{
    public function collection()
    {
        $subscriptions = Subscription::with('user')->get();

        $exportData = $subscriptions->map(function ($sub) {
            return [
                $sub->user_id,
                $sub->user->name ?? '—',
                $sub->plan,
                $sub->provider,
                $sub->status,
                $sub->start_date,
                $sub->end_date ?? '-',
                $sub->created_at,
                $sub->updated_at ?? '-',
            ];
        });

        return new Collection($exportData);
    }
    public function headings(): array
    {
        return [
            'User ID',
            'User Name',
            'Plan',
            'Provider',
            'Status',
            'Start Date',
            'End Date',
            'Created At',
            'Updated At',
        ];
    }
}

class TicketsSheet implements FromCollection, WithHeadings
{
    public function collection()
    {
        $tickets = Ticket::with(['user', 'manager'])->get();
        $exportData = $tickets->map(function ($tick) {
            return [
                'User Name' => $tick->user->name ?? '-',
                'Manager Name' => $tick->manager->user->name ?? '-',
                'Subject' => $tick->subject,
                'Description' => $tick->description,
                'Response' => $tick->response ?? '-',
                'Status' => $tick->status,
                'Created at' => $tick->created_at,
                'Updated at' => $tick->updated_at ?? '-',
            ];
        });
        return new Collection($exportData);
    }
    public function headings(): array
    {
        return [
            'User Name',
            'Manager Name',
            'Subject',
            'Description',
            'Response',
            'Status',
            'Created At',
            'Updated At',
        ];
    }
}

class ProjectsSheet implements FromCollection, WithHeadings
{
    public function collection()
    {
        $projects = Project::with('user')->get();
        $exportData = $projects->map(function ($proj) {
            return [
                $proj->user->name,
                $proj->title,
                $proj->description,
                $proj->budget,
                $proj->category,
                $proj->tags,
                $proj->status,
                $proj->deadline ?? '-',
                $proj->created_at,
                $proj->updated_at,
            ];
        });
        return new Collection($exportData);
    }
    public function headings(): array
    {
        return [
            'Client Name',
            'Project Title',
            'Description',
            'Budget',
            'Category',
            'Tags',
            'Status',
            'Deadline',
            'Created At',
            'Updated At',
        ];
    }
}

class FreelancersSheet implements FromCollection, WithHeadings
{
    public function collection()
    {
        $freelancers = User::where('role', 'freelancer')->get();
        $exportData = $freelancers->map(function ($freel) {
            return [
                $freel->name,
                $freel->email,
                $freel->university,
                $freel->study_year,
                $freel->created_at,
                $freel->updated_at,
            ];
        });
        return new Collection($exportData);
    }
    public function headings(): array
    {
        return [
            'Freelancer Name',
            'Email',
            'University',
            'Study Year',
            'Created At',
            'Updated At',
        ];
    }
}
class ClientsSheet implements FromCollection, WithHeadings
{
    public function collection()
    {
        $clients = User::where('role', 'user')->get();
        $exportData = $clients->map(function ($client) {
            return [
                $client->name,
                $client->email,
                $client->created_at,
                $client->updated_at,
            ];
        });
        return new Collection($exportData);
    }
    public function headings(): array
    {
        return [
            'Client Name',
            'Email',
            'Created At',
            'Updated At',
        ];
    }
}
