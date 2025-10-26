import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import EngineerLayout from '@/layouts/engineer-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { CalendarIcon, CheckCircle, Clock, AlertCircle } from 'lucide-react';
import { format } from 'date-fns';
import { cn } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';

interface Schedule {
    id: number;
    date: string;
    project: {
        id: number;
        name: string;
    };
    location: string | null;
    notes: string | null;
}

interface Timesheet {
    id: number;
    date: string;
    project: {
        id: number;
        name: string;
    };
    schedule: {
        id: number;
    } | null;
    notes: string | null;
    approved: boolean;
}

interface Project {
    id: number;
    name: string;
}

interface Props {
    schedules: Schedule[];
    timesheets: Timesheet[];
    projects: Project[];
    today: string;
}

export default function TimesheetIndex({ schedules, timesheets, projects, today }: Props) {
    const [selectedDate, setSelectedDate] = useState<Date | undefined>(new Date());
    const [isExceptional, setIsExceptional] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        date: today,
        project_id: '',
        schedule_id: '',
        notes: '',
    });

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/engineer/dashboard' },
        { title: 'Timesheet', href: '/engineer/timesheet' },
    ];

    // Find schedule for selected date
    const scheduleForDate = schedules.find(
        s => s.date === format(selectedDate || new Date(), 'yyyy-MM-dd')
    );

    // Find timesheet for selected date
    const timesheetForDate = timesheets.find(
        t => t.date === format(selectedDate || new Date(), 'yyyy-MM-dd')
    );

    const handleDateSelect = (date: Date | undefined) => {
        if (!date) return;

        setSelectedDate(date);
        const formattedDate = format(date, 'yyyy-MM-dd');
        setData('date', formattedDate);

        const schedule = schedules.find(s => s.date === formattedDate);

        if (schedule) {
            setData({
                date: formattedDate,
                project_id: schedule.project.id.toString(),
                schedule_id: schedule.id.toString(),
                notes: '',
            });
            setIsExceptional(false);
        } else {
            setData({
                date: formattedDate,
                project_id: '',
                schedule_id: '',
                notes: '',
            });
            setIsExceptional(true);
        }
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/engineer/timesheet', {
            onSuccess: () => {
                reset('notes');
            },
        });
    };

    return (
        <EngineerLayout breadcrumbs={breadcrumbs}>
            <Head title="Timesheet" />

            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">Timesheet</h1>
                    <p className="text-muted-foreground">
                        Submit your daily timesheet entries
                    </p>
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    {/* Submit Timesheet Card */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Submit Timesheet</CardTitle>
                            <CardDescription>
                                Select a date and submit your timesheet
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={handleSubmit} className="space-y-4">
                                {/* Date Picker */}
                                <div className="space-y-2">
                                    <Label>Date</Label>
                                    <Popover>
                                        <PopoverTrigger asChild>
                                            <Button
                                                variant="outline"
                                                className={cn(
                                                    'w-full justify-start text-left font-normal',
                                                    !selectedDate && 'text-muted-foreground'
                                                )}
                                            >
                                                <CalendarIcon className="mr-2 h-4 w-4" />
                                                {selectedDate ? (
                                                    format(selectedDate, 'PPP')
                                                ) : (
                                                    <span>Pick a date</span>
                                                )}
                                            </Button>
                                        </PopoverTrigger>
                                        <PopoverContent className="w-auto p-0" align="start">
                                            <Calendar
                                                mode="single"
                                                selected={selectedDate}
                                                onSelect={handleDateSelect}
                                                autoFocus={true}
                                            />
                                        </PopoverContent>
                                    </Popover>
                                    {errors.date && (
                                        <p className="text-sm text-destructive">{errors.date}</p>
                                    )}
                                </div>

                                {/* Schedule Info or Warning */}
                                {scheduleForDate ? (
                                    <div className="rounded-lg border border-green-200 bg-green-50 p-4">
                                        <div className="flex items-start gap-3">
                                            <CheckCircle className="h-5 w-5 text-green-600 mt-0.5" />
                                            <div className="flex-1">
                                                <p className="font-medium text-green-900">
                                                    Scheduled Job
                                                </p>
                                                <p className="text-sm text-green-700 mt-1">
                                                    {scheduleForDate.project.name}
                                                </p>
                                                {scheduleForDate.location && (
                                                    <p className="text-sm text-green-600 mt-1">
                                                        Location: {scheduleForDate.location}
                                                    </p>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="rounded-lg border border-amber-200 bg-amber-50 p-4">
                                        <div className="flex items-start gap-3">
                                            <AlertCircle className="h-5 w-5 text-amber-600 mt-0.5" />
                                            <div className="flex-1">
                                                <p className="font-medium text-amber-900">
                                                    No Schedule Found
                                                </p>
                                                <p className="text-sm text-amber-700 mt-1">
                                                    Please select a project for this exceptional entry
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {/* Project Selection (only for exceptional cases) */}
                                {!scheduleForDate && (
                                    <div className="space-y-2">
                                        <Label htmlFor="project">Project *</Label>
                                        <Select
                                            value={data.project_id}
                                            onValueChange={(value) => setData('project_id', value)}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select a project" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {projects.map((project) => (
                                                    <SelectItem
                                                        key={project.id}
                                                        value={project.id.toString()}
                                                    >
                                                        {project.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        {errors.project_id && (
                                            <p className="text-sm text-destructive">{errors.project_id}</p>
                                        )}
                                    </div>
                                )}

                                {/* Notes */}
                                <div className="space-y-2">
                                    <Label htmlFor="notes">Notes (optional)</Label>
                                    <Textarea
                                        id="notes"
                                        placeholder="Add any additional notes..."
                                        value={data.notes}
                                        onChange={(e) => setData('notes', e.target.value)}
                                        rows={4}
                                    />
                                    {errors.notes && (
                                        <p className="text-sm text-destructive">{errors.notes}</p>
                                    )}
                                </div>

                                {/* Submit Button */}
                                <Button
                                    type="submit"
                                    className="w-full"
                                    disabled={processing || !!timesheetForDate}
                                >
                                    {timesheetForDate ? 'Already Submitted' : 'Submit Timesheet'}
                                </Button>

                                {timesheetForDate && (
                                    <p className="text-sm text-muted-foreground text-center">
                                        Timesheet already submitted for this date
                                    </p>
                                )}
                            </form>
                        </CardContent>
                    </Card>

                    {/* Recent Timesheets */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Timesheets</CardTitle>
                            <CardDescription>
                                Your timesheet entries for this month
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                {timesheets.length === 0 ? (
                                    <div className="text-center py-8 text-muted-foreground">
                                        <Clock className="h-12 w-12 mx-auto mb-2 opacity-50" />
                                        <p>No timesheets submitted yet</p>
                                    </div>
                                ) : (
                                    timesheets.map((timesheet) => (
                                        <div
                                            key={timesheet.id}
                                            className="flex items-start justify-between p-4 border rounded-lg"
                                        >
                                            <div className="flex-1">
                                                <div className="flex items-center gap-2 mb-1">
                                                    <p className="font-medium">
                                                        {format(new Date(timesheet.date), 'PPP')}
                                                    </p>
                                                    {timesheet.approved ? (
                                                        <Badge variant="default">Approved</Badge>
                                                    ) : (
                                                        <Badge variant="secondary">Pending</Badge>
                                                    )}
                                                </div>
                                                <p className="text-sm text-muted-foreground">
                                                    {timesheet.project.name}
                                                </p>
                                                {timesheet.notes && (
                                                    <p className="text-sm text-muted-foreground mt-2">
                                                        {timesheet.notes}
                                                    </p>
                                                )}
                                            </div>
                                        </div>
                                    ))
                                )}
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </EngineerLayout>
    );
}
