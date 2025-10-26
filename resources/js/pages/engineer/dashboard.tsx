import { Head } from '@inertiajs/react';
import EngineerLayout from '@/layouts/engineer-layout';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { AlertCircle, Calendar, FileText, TrendingUp } from 'lucide-react';

interface Schedule {
    id: number;
    project: {
        name: string;
    };
    date: string;
    role: string;
    status: string;
}

interface Stats {
    upcomingShifts: number;
    shiftsThisMonth: number;
    pendingReports: number;
    earnings: string;
}

interface DashboardProps {
    upcomingShifts: Schedule[];
    pendingReports: Schedule[];
    stats: Stats;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/engineer/dashboard',
    },
];

export default function Dashboard({ upcomingShifts, pendingReports, stats }: DashboardProps) {
    return (
        <EngineerLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />

            <div className="flex flex-1 flex-col gap-4 p-4 pt-0">
                {/* Stats Grid */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Upcoming Shifts
                            </CardTitle>
                            <Calendar className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.upcomingShifts}</div>
                            <p className="text-xs text-muted-foreground">
                                Next 7 days
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Shifts This Month
                            </CardTitle>
                            <TrendingUp className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.shiftsThisMonth}</div>
                            <p className="text-xs text-muted-foreground">
                                Completed
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Pending Reports
                            </CardTitle>
                            <AlertCircle className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stats.pendingReports}</div>
                            <p className="text-xs text-muted-foreground">
                                Action required
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                This Month Earnings
                            </CardTitle>
                            <FileText className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">£{stats.earnings}</div>
                            <p className="text-xs text-muted-foreground">
                                Based on completed shifts
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Quick Actions */}
                <Card>
                    <CardHeader>
                        <CardTitle>Quick Actions</CardTitle>
                        <CardDescription>
                            Frequently used actions for easy access
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="flex flex-wrap gap-3">
                        <Button>Submit Report</Button>
                        <Button variant="outline">View Schedules</Button>
                        <Button variant="outline">Generate Invoice</Button>
                    </CardContent>
                </Card>

                {/* Upcoming Shifts */}
                <Card>
                    <CardHeader>
                        <CardTitle>Upcoming Shifts</CardTitle>
                        <CardDescription>
                            Your scheduled shifts for the next 7 days
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {upcomingShifts && upcomingShifts.length > 0 ? (
                            <div className="space-y-3">
                                {upcomingShifts.map((shift) => (
                                    <div
                                        key={shift.id}
                                        className="flex items-center justify-between rounded-lg border p-4 hover:bg-accent"
                                    >
                                        <div>
                                            <h4 className="font-medium">{shift.project.name}</h4>
                                            <p className="text-sm text-muted-foreground">
                                                {shift.role} • {shift.date}
                                            </p>
                                        </div>
                                        <Badge variant="secondary">Upcoming</Badge>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <p className="text-sm text-muted-foreground">
                                No upcoming shifts scheduled.
                            </p>
                        )}
                    </CardContent>
                </Card>

                {/* Pending Reports Alert */}
                {pendingReports && pendingReports.length > 0 && (
                    <Card className="border-amber-200 bg-amber-50 dark:border-amber-900 dark:bg-amber-950">
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <AlertCircle className="h-5 w-5 text-amber-600 dark:text-amber-500" />
                                <CardTitle>Reports Needed</CardTitle>
                            </div>
                            <CardDescription className="text-amber-900 dark:text-amber-300">
                                You have {pendingReports.length} shift{pendingReports.length > 1 ? 's' : ''} that need{pendingReports.length === 1 ? 's' : ''} a report
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Button>Submit Reports</Button>
                        </CardContent>
                    </Card>
                )}
            </div>
        </EngineerLayout>
    );
}
