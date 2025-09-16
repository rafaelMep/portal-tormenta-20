import { PropsWithChildren } from 'react';
import { Link } from '@inertiajs/react';
import T20Logo from '@/Components/T20Logo';

export default function GuestLayout({ children }: PropsWithChildren) {
    return (
        <div className="min-h-screen relative">
            {/* Fundo com “vibez” de Tormenta: vermelho/escuro + textura sutil */}
            <div
                className="absolute inset-0 -z-10 bg-gradient-to-br from-[#1a0b0b] via-[#0b0b10] to-[#1f0a0a] opacity-95" />
            <div
                className="absolute inset-0 -z-10 [background-image:radial-gradient(ellipse_at_center,rgba(225,29,72,0.12)_0%,rgba(0,0,0,0)_60%)]" />
            <div className="absolute inset-0 -z-10 [background-image:radial-gradient(#ffffff22_1px,transparent_1px)] [background-size:24px_24px] [background-position:0_0]" />

            <div className="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
                <Link href="/" className="inline-flex items-center gap-2">
                    <T20Logo className="text-slate-100" />
                </Link>

                <div className="mx-auto mt-8 w-full max-w-md">
                    <div className="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md shadow-2xl">
                        <div className="p-6 sm:p-8">
                            {children}
                        </div>
                    </div>
                    <p className="mt-6 text-center text-sm text-white/70">
                        Portal de Fichas • <span className="text-rose-400 font-semibold">Tormenta 20</span>
                    </p>
                </div>
            </div>
        </div>
    );
}
