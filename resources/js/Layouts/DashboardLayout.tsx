import { PropsWithChildren, useState, useEffect, useRef } from 'react';
import { Link, usePage } from '@inertiajs/react';
import T20Logo from '@/Components/T20Logo';

type User = { name: string; email: string };

export default function DashboardLayout({ children }: PropsWithChildren) {
    const [openMobile, setOpenMobile] = useState(false);
    const [menuOpen, setMenuOpen] = useState(false);
    const menuRef = useRef<HTMLDivElement>(null);

    const { props } = usePage();
    const user = (props as any).auth?.user as User | undefined;

    const is = (name: string) => route().current(name);

    const nav = [
        {
            label: 'Jogador',
            children: [
                { label: 'Início', href: route('dashboard.player.index'), active: is('dashboard.player.index') },
                { label: 'Fichas de personagem', href: route('dashboard.player.characters.index'), active: is('dashboard.player.characters.*') },
            ],
        },
        {
            label: 'Mestre',
            children: [
                { label: 'Início', href: route('dashboard.master.index'), active: is('dashboard.master.index') },
            ],
        },
    ];

    useEffect(() => {
        function onDocClick(e: MouseEvent) {
            if (!menuRef.current) return;
            if (!menuRef.current.contains(e.target as Node)) setMenuOpen(false);
        }
        document.addEventListener('click', onDocClick);
        return () => document.removeEventListener('click', onDocClick);
    }, []);

    const initials =
        (user?.name ?? '')
            .trim()
            .split(/\s+/)
            .map((p) => p[0])
            .filter(Boolean)
            .slice(0, 2)
            .join('')
            .toUpperCase() || 'U';

    return (
        <div
            className="
        min-h-[100dvh] text-white
        bg-[#0b1020]
        bg-[radial-gradient(1200px_600px_at_20%_-10%,rgba(244,63,94,0.06),transparent_60%),radial-gradient(1000px_500px_at_110%_120%,rgba(56,189,248,0.06),transparent_60%)]
      "
        >
            {/* Topbar */}
            <header
                className="sticky top-0 z-30 relative bg-black/40 backdrop-blur-md supports-[backdrop-filter]:bg-black/30 /* glows no topo (rose + sky) */ before:pointer-events-none before:absolute before:inset-x-0 before:-top-10 before:h-20
                            before:bg-[radial-gradient(28rem_10rem_at_15%_0,rgba(244,63,94,0.22),transparent_60%)] after:pointer-events-none after:absolute after:inset-x-0 after:-top-8 after:h-16
                            after:bg-[radial-gradient(24rem_9rem_at_85%_0,rgba(56,189,248,0.18),transparent_60%)]"
            >
                {/* hairline brilhante na base */}
                <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/25 to-transparent" />

                <div className="w-full px-6 xl:px-10 py-3 flex items-center justify-between border-b border-white/10">
                    <Link href="/" className="flex items-center gap-2">
                        <T20Logo />
                    </Link>

                    <div className="flex items-center gap-3">
                        {user && (
                            <div className="relative hidden md:block" ref={menuRef}>
                                <button
                                    onClick={() => setMenuOpen((v) => !v)}
                                    className="inline-flex items-center gap-2 rounded-xl px-3 py-2 bg-white/10 hover:bg-white/15 ring-1 ring-white/10 transition"
                                >
                                    <span className="inline-flex h-7 w-7 items-center justify-center rounded-full bg-rose-600 text-xs font-bold shadow-inner shadow-black/30">
                                        {initials}
                                    </span>
                                    <span className="text-sm font-semibold">{user.name}</span>
                                    <svg className={`h-4 w-4 transition ${menuOpen ? 'rotate-180' : ''}`} viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fillRule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.085l3.71-3.854a.75.75 0 111.08 1.04l-4.24 4.4a.75.75 0 01-1.08 0l-4.24-4.4a.75.75 0 01.02-1.06z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                </button>

                                {menuOpen && (
                                    <div
                                        className="absolute right-0 mt-2 w-52 overflow-hidden rounded-2xl border border-white/10 bg-black/85 backdrop-blur-md shadow-xl shadow-black/30"
                                        role="menu"
                                    >
                                        <Link
                                            href={route('profile.edit')}
                                            className="block px-4 py-2.5 text-sm hover:bg-white/10"
                                            onClick={() => setMenuOpen(false)}
                                        >
                                            Perfil
                                        </Link>
                                        <Link
                                            href={route('logout')}
                                            method="post"
                                            as="button"
                                            className="block w-full text-left px-4 py-2.5 text-sm hover:bg-white/10"
                                            onClick={() => setMenuOpen(false)}
                                        >
                                            Sair
                                        </Link>
                                    </div>
                                )}
                            </div>
                        )}

                        {/* Trigger sidebar (mobile) */}
                        <button
                            onClick={() => setOpenMobile((v) => !v)}
                            className="md:hidden inline-flex items-center gap-2 rounded-xl px-3 py-2 bg-white/10 hover:bg-white/15 ring-1 ring-white/10"
                            aria-expanded={openMobile}
                            aria-controls="sidebar"
                        >
                            Menu
                        </button>
                    </div>
                </div>
            </header>

            {/* Main */}
            <div className="w-full px-6 xl:px-10 py-6 grid grid-cols-1 md:grid-cols-[260px,1fr] 2xl:grid-cols-[280px,1fr] gap-6">
                {/* Sidebar */}
                <aside
                    id="sidebar"
                    className={`group relative z-40 md:z-auto md:sticky md:top-[72px] md:h-[calc(100vh-50%)] md:overflow-y-auto
                                rounded-2xl border border-white/10 bg-white/[0.045] backdrop-blur-sm p-3 transition ${openMobile ? '' : 'hidden md:block'}`}>
                    {/* glow sutil nas bordas */}
                    <div className="pointer-events-none absolute inset-0 rounded-2xl ring-1 ring-white/5" />

                    <nav className="space-y-4">
                        {nav.map((section, i) => (
                            <div key={i}>
                                <div className="px-2 pb-2 text-[11px] uppercase tracking-wider text-white/50">
                                    {section.label}
                                </div>
                                <div className="flex flex-col gap-1.5">
                                    {section.children?.map((item, j) => (
                                        <Link
                                            key={j}
                                            href={item.href!}
                                            className={`relative rounded-xl px-3 py-2 text-sm transition ring-1 ring-transparent ${item.active
                                                ? 'bg-gradient-to-r from-rose-600/25 to-rose-500/10 text-rose-200 ring-rose-400/30 shadow-[inset_0_0_0_1px_rgba(255,255,255,0.05)]'
                                                : 'hover:bg-white/10 text-white/90 hover:text-white'}`}
                                        >
                                            {/* barra de acento à esquerda quando ativo */}
                                            <span
                                                className={`absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r ${item.active ? 'bg-rose-400/70' : 'bg-transparent'}`}
                                            />
                                            {item.label}
                                        </Link>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </nav>

                    {/* Conta (mobile) */}
                    {user && (
                        <div className="mt-6 md:hidden border-t border-white/10 pt-4 space-y-2">
                            <div className="px-2 text-[11px] uppercase tracking-wider text-white/50">Conta</div>
                            <Link href={route('profile.edit')} className="block rounded-xl px-3 py-2 text-sm hover:bg-white/10">
                                Perfil
                            </Link>
                            <Link
                                href={route('logout')}
                                method="post"
                                as="button"
                                className="w-full rounded-xl px-3 py-2 text-left text-sm hover:bg-white/10"
                            >
                                Sair
                            </Link>
                        </div>
                    )}
                </aside>

                {/* Conteúdo */}
                <main className="min-w-0">
                    {/* container do conteúdo com cartão leve opcional */}
                    <div className="rounded-2xl border border-white/10 bg-white/[0.03] p-5">
                        {children}
                    </div>
                </main>
            </div>

            {/* Overlay no mobile ao abrir sidebar */}
            {openMobile && (
                <button
                    onClick={() => setOpenMobile(false)}
                    className="fixed inset-0 z-30 bg-black/50 md:hidden"
                    aria-label="Fechar menu"
                />
            )}
        </div>
    );
}
