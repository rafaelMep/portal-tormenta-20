import { Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';

export default function Index() {
    const characters: Array<{ id: number; name: string; level: number; clazz: string }> = [];

    return (
        <DashboardLayout>
            <div className="flex items-start justify-between gap-3">
                <h1 className="text-2xl font-bold">Minhas fichas</h1>
                <Link
                    href={route('dashboard.player.characters.create')}
                    className="rounded-lg px-4 py-2 bg-rose-600 hover:bg-rose-500 ring-1 ring-rose-400/50"
                >
                    Nova ficha
                </Link>
            </div>

            <div className="mt-6 space-y-3">
                {characters.length === 0 ? (
                    <div className="rounded-xl border border-white/10 bg-white/5 p-6 text-white/80">
                        Você ainda não tem fichas. Clique em <strong>Nova ficha</strong> para começar.
                    </div>
                ) : (
                    <ul className="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        {characters.map(c => (
                            <li key={c.id} className="rounded-xl border border-white/10 bg-white/5 p-4">
                                <div className="font-semibold">{c.name}</div>
                                <div className="text-white/70 text-sm">Nível {c.level} — {c.clazz}</div>
                                <div className="mt-3">
                                    <Link
                                        href={route('dashboard.player.characters.show', { id: c.id })}
                                        className="text-sm text-rose-300 hover:text-rose-200 underline underline-offset-2"
                                    >
                                        Abrir ficha
                                    </Link>
                                </div>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </DashboardLayout>
    );
}
