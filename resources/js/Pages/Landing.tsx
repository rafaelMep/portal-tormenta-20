// resources/js/Pages/Landing.tsx
import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import Modal from '@/Components/Modal';
import LoginForm from '@/Pages/Auth/LoginForm';
import RegisterForm from '@/Pages/Auth/RegisterForm';
import T20Logo from '@/Components/T20Logo';

export default function Landing() {
    const [loginOpen, setLoginOpen] = useState(false);
    const [registerOpen, setRegisterOpen] = useState(false);

    return (
        <div className="relative min-h-screen text-white">
            <Head title="Portal Tormenta 20" />

            {/* Fundo com a capa */}
            <div className="absolute inset-0 -z-10">
                <img
                    src="/images/tormenta20-cover.png"
                    alt="Capa Tormenta 20"
                    className="h-full w-full object-cover brightness-[.45] saturate-75"
                />
                {/* véu para legibilidade */}
                <div className="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/80" />
            </div>

            {/* Top bar */}
            <header className="flex items-center justify-between px-6 md:px-10 py-5">
                <Link href="/" className="flex items-center gap-2">
                    <T20Logo />
                </Link>

                <nav className="flex items-center gap-3">
                    <button
                        onClick={() => setLoginOpen(true)}
                        className="rounded-md px-4 py-2 text-sm font-semibold bg-white/10 hover:bg-white/20 ring-1 ring-white/15 transition"
                    >
                        Entrar
                    </button>
                    <button
                        onClick={() => setRegisterOpen(true)}
                        className="rounded-md px-4 py-2 text-sm font-semibold bg-rose-600 hover:bg-rose-500 ring-1 ring-rose-400/50 transition"
                    >
                        Criar conta
                    </button>
                </nav>
            </header>

            {/* Hero */}
            <section className="mx-auto max-w-3xl px-6 md:px-10 mt-24 md:mt-32">
                <h1 className="text-4xl md:text-6xl font-extrabold leading-tight drop-shadow">
                    <span className="block">Portal</span>
                    <span className="text-rose-400">Tormenta 20</span>
                </h1>

                <p className="mt-4 text-lg text-white/85 max-w-prose">
                    Um portal para <strong>jogadores</strong> e <strong>mestres</strong> gerenciarem suas
                    <strong> campanhas</strong> de Tormenta 20: crie personagens, evolua do nível 1 ao 20,
                    organize fichas, sessões, NPCs, itens e muito mais — tudo em um só lugar.
                </p>

                <div className="mt-6 flex gap-3">
                    <button
                        onClick={() => setRegisterOpen(true)}
                        className="rounded-lg px-6 py-3 font-semibold bg-rose-600 hover:bg-rose-500 ring-1 ring-rose-400/50"
                    >
                        Começar agora
                    </button>
                    <button
                        onClick={() => setLoginOpen(true)}
                        className="rounded-lg px-6 py-3 font-semibold bg-white/10 hover:bg-white/20 ring-1 ring-white/15"
                    >
                        Já tenho conta
                    </button>
                </div>
            </section>

            {/* Modais */}
            <Modal open={loginOpen} onClose={() => setLoginOpen(false)} title="Entrar" maxWidth="sm">
                <LoginForm />
            </Modal>

            <Modal open={registerOpen} onClose={() => setRegisterOpen(false)} title="Criar conta" maxWidth="sm">
                <RegisterForm />
            </Modal>
        </div>
    );
}
