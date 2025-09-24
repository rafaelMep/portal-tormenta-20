import { Link } from '@inertiajs/react';

type Props = {
    title: string;
    subtitle?: string;
    href?: string;
    icon?: React.ReactNode;
    accent?: 'rose' | 'emerald' | 'sky' | 'amber' | 'violet' | 'slate';
};

const accents: Record<NonNullable<Props['accent']>, string> = {
    rose: 'from-rose-500/20 to-rose-500/5 ring-rose-400/30',
    emerald: 'from-emerald-500/20 to-emerald-500/5 ring-emerald-400/30',
    sky: 'from-sky-500/20 to-sky-500/5 ring-sky-400/30',
    amber: 'from-amber-500/20 to-amber-500/5 ring-amber-400/30',
    violet: 'from-violet-500/20 to-violet-500/5 ring-violet-400/30',
    slate: 'from-slate-500/20 to-slate-500/5 ring-slate-400/30',
};

export default function DashboardCard({ title, subtitle, href, icon, accent = 'rose' }: Props) {
    const base = `group relative overflow-hidden rounded-xl border ring-1
                bg-gradient-to-br ${accents[accent]} border-white/10 text-white
                hover:ring-white/40 transition`;

    const inner = (
        <div className="p-5 h-full flex flex-col gap-3">
            <div className="flex items-center gap-3">
                {icon && <div className="text-2xl">{icon}</div>}
                <h3 className="text-lg font-semibold leading-tight">{title}</h3>
            </div>
            {subtitle && <p className="text-sm text-white/80">{subtitle}</p>}
            <div className="mt-auto text-sm text-white/70 group-hover:text-white/90">
                {href ? 'Abrir →' : 'Em breve'}
            </div>
        </div>
    );

    return href ? (
        <Link href={href} className={base}>
            {inner}
        </Link>
    ) : (
        <div className={`${base} opacity-80`}>
            {inner}
        </div>
    );
}
