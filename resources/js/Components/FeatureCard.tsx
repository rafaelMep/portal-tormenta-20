import { Link } from '@inertiajs/react';

type Props = {
    title: string;
    subtitle?: string;
    href?: string;
    icon?: React.ReactNode;
    accent?: 'rose' | 'amber' | 'violet' | 'sky' | 'emerald' | 'slate';
    bgUrl?: string;
    cta?: string;
    overlay?: 'soft' | 'mid' | 'hard';
};

const grad: Record<NonNullable<Props['accent']>, string> = {
    rose: 'from-rose-600/70 to-fuchsia-500/30',
    amber: 'from-amber-500/80 to-orange-500/30',
    violet: 'from-violet-600/70 to-purple-500/30',
    sky: 'from-sky-500/80 to-cyan-500/30',
    emerald: 'from-emerald-600/70 to-green-500/30',
    slate: 'from-slate-500/70 to-zinc-600/30',
};

const ring: Record<NonNullable<Props['accent']>, string> = {
    rose: 'ring-rose-400/40',
    amber: 'ring-amber-400/40',
    violet: 'ring-violet-400/40',
    sky: 'ring-sky-400/40',
    emerald: 'ring-emerald-400/40',
    slate: 'ring-slate-400/40',
};

const overlays = {
    soft: 'bg-gradient-to-t from-black/60 via-black/35 to-transparent',
    mid: 'bg-gradient-to-t from-black/70 via-black/45 to-transparent',
    hard: 'bg-gradient-to-t from-black/80 via-black/55 to-transparent',
};

export default function FeatureCard({
    title,
    subtitle,
    href,
    icon,
    accent = 'rose',
    bgUrl,
    cta = 'Abrir →',
    overlay = 'mid',
}: Props) {
    const Wrapper: any = href ? Link : 'div';

    return (
        <Wrapper
            href={href as any}
            className={`group relative block rounded-2xl p-[1px] bg-gradient-to-br ${grad[accent]}
                  shadow-lg hover:shadow-2xl hover:shadow-black/30 transition-transform
                  hover:-translate-y-0.5 will-change-transform`}
        >
            <div className="relative overflow-hidden rounded-[calc(1rem-1px)] bg-white/5 backdrop-blur-sm ring-1 ring-white/10 min-h-[180px]">
                {/* BG opcional */}
                {!bgUrl && (
                    <div className={`absolute inset-0 -z-10 ${overlays[overlay]} transition`} />
                )}

                {bgUrl && (
                    <>
                        <img
                            src={bgUrl}
                            alt=""
                            className="absolute inset-0 -z-10 h-full w-full object-cover opacity-45 group-hover:opacity-55 transition"
                        />
                        <div className="absolute inset-0 -z-10 bg-gradient-to-t from-black/70 via-black/40 to-transparent" />
                    </>
                )}

                {/* Shine */}
                <div className="pointer-events-none absolute inset-y-0 -left-1/2 w-1/2 skew-x-[-20deg] bg-white/10 opacity-0
                        group-hover:opacity-100 group-hover:translate-x-[220%] transition duration-700" />

                {/* Conteúdo */}
                <div className="p-5 h-full flex flex-col">
                    <div className="flex items-center gap-3">
                        {icon && (
                            <div className="grid h-10 w-10 place-items-center rounded-xl bg-black/40 ring-1 ring-white/10 text-2xl">
                                {icon}
                            </div>
                        )}
                        <h3 className="text-lg font-semibold drop-shadow">{title}</h3>
                    </div>

                    {subtitle && <p className="mt-2 text-sm text-white/85">{subtitle}</p>}

                    <div className="mt-auto pt-4">
                        <span
                            className={`inline-flex items-center gap-2 rounded-md px-3 py-1.5 text-sm
                          bg-white/10 hover:bg-white/20 ${ring[accent]} transition`}
                        >
                            {cta} <span aria-hidden>→</span>
                        </span>
                    </div>
                </div>
            </div>
        </Wrapper>
    );
}
