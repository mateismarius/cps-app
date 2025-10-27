import { Head, Link, usePage } from '@inertiajs/react'
import type { SharedData } from '@/types'
import { dashboard, login} from '@/routes'
import AppLogo from '@/components/app-logo';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props

    return (
        <>
            <Head title="CPS Network ERP Platform">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link
                    href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600"
                    rel="stylesheet"
                />
            </Head>

            <div className="min-h-screen flex flex-col bg-background text-foreground">
                {/* Header */}
                <header className="flex items-center justify-between px-6 py-4 border-b border-border">
                    <div className="flex items-center space-x-3">
                        <AppLogo />
                        <span className="text-lg font-semibold tracking-tight">

            </span>
                    </div>

                </header>

                {/* Hero Section */}
                <main className="flex flex-col lg:flex-row flex-1 items-center justify-center px-8 py-16">
                    <div className="flex-1 max-w-xl text-center lg:text-left space-y-5">
                        <h1 className="text-4xl font-bold tracking-tight text-primary">
                            Simplify Your Operations.
                        </h1>
                        <p className="text-muted-foreground text-lg leading-relaxed">
                            CPS-Enterprise Resource Planning is your centralized platform for managing projects,
                            schedules, invoices and teams — built for efficiency and clarity.
                        </p>

                        <div className="flex justify-center lg:justify-start gap-4 pt-4">
                            {auth.user ? (
                                <Link
                                    href={dashboard()}
                                    className="rounded-md bg-primary text-primary-foreground px-6 py-2 font-medium hover:bg-primary/90 transition"
                                >
                                    Go to Dashboard
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href={login()}
                                        className="rounded-md bg-primary text-primary-foreground px-6 py-2 font-medium hover:bg-primary/90 transition"
                                    >
                                        Sign In
                                    </Link>
                                </>
                            )}
                        </div>
                    </div>
                </main>

                {/* Footer */}
                <footer className="border-t border-border py-4 text-center text-xs text-muted-foreground">
                    © {new Date().getFullYear()} CPS Network · ERP Platform
                </footer>
            </div>
        </>
    )
}
