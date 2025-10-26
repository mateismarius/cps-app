
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { CalendarDays, FileText, LayoutDashboard, Wallet, Receipt, Clock } from 'lucide-react';
import AppLogo from './app-logo';
import { Badge } from './ui/badge';

export default function EngineerSidebar() {
    const { pendingReportsCount } = usePage().props as { pendingReportsCount?: number };

    const mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/engineer/dashboard',
            icon: LayoutDashboard,
        },
        {
            title: 'My Timesheet',
            href: '/engineer/timesheet',
            icon: Clock,
        },
        {
            title: 'My Schedules',
            href: '/engineer/schedules',
            icon: CalendarDays,
        },
        {
            title: 'Reports',
            href: '/engineer/reports',
            icon: FileText,
            // label: pendingReportsCount && pendingReportsCount > 0 ? String(pendingReportsCount) : undefined,
        },
        {
            title: 'Invoices',
            href: '/engineer/invoices',
            icon: Receipt,
        },
        {
            title: 'My Finance',
            href: '/engineer/finance',
            icon: Wallet,
        },
    ];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/engineer/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
