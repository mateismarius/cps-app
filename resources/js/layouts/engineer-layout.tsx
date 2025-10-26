import { AppShell } from '@/components/app-shell';
import { AppContent } from '@/components/app-content';
import EngineerSidebar from '@/components/engineer-sidebar';
import EngineerHeader from '@/components/engineer-header';
import { type BreadcrumbItem } from '@/types';
import { type PropsWithChildren } from 'react';

interface EngineerLayoutProps {
    breadcrumbs?: BreadcrumbItem[];
}

export default function EngineerLayout({
                                           breadcrumbs = [],
                                           children,
                                       }: PropsWithChildren<EngineerLayoutProps>) {
    return (
        <AppShell variant="sidebar">
            <EngineerSidebar />
            <AppContent variant="sidebar" className="overflow-x-hidden">
                <EngineerHeader breadcrumbs={breadcrumbs} />
                {children}
            </AppContent>
        </AppShell>
    );
}
