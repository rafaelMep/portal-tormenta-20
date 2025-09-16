import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import Checkbox from '@/Components/Checkbox';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Login({ status, canResetPassword = true }: { status?: string; canResetPassword?: boolean }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <>
            <Head title="Entrar" />

            <h1 className="mb-1 text-2xl font-extrabold tracking-tight text-white">Entrar</h1>
            <p className="mb-6 text-sm text-white/75">Acesse o Portal <span className="text-rose-400 font-semibold">Tormenta 20</span></p>

            {status && (
                <div className="mb-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-emerald-300">
                    {status}
                </div>
            )}

            <form onSubmit={submit} className="space-y-5">
                <div>
                    <InputLabel htmlFor="email" value="E-mail" className="text-white" />
                    <TextInput
                        id="email"
                        type="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        onChange={(e) => setData('email', e.target.value)}
                        required
                    />
                    <InputError message={errors.email} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="password" value="Senha" className="text-white" />
                    <TextInput
                        id="password"
                        type="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        autoComplete="current-password"
                        onChange={(e) => setData('password', e.target.value)}
                        required
                    />
                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="flex items-center justify-between">
                    <label className="inline-flex items-center gap-2 select-none">
                        <Checkbox
                            name="remember"
                            checked={data.remember}
                            onChange={(e) => setData('remember', e.target.checked)}
                        />
                        <span className="text-sm text-white/80">Lembrar de mim</span>
                    </label>

                    {canResetPassword && (
                        <Link
                            href={route('password.request')}
                            className="text-sm font-medium text-rose-300 hover:text-rose-200 transition-colors"
                        >
                            Esqueci minha senha
                        </Link>
                    )}
                </div>

                <PrimaryButton className="w-full justify-center bg-rose-600 hover:bg-rose-500 focus:ring-rose-400" disabled={processing}>
                    Entrar
                </PrimaryButton>

                <p className="text-center text-sm text-white/80">
                    Não tem conta?{' '}
                    <Link href={route('register')} className="font-semibold text-rose-300 hover:text-rose-200">
                        Criar conta
                    </Link>
                </p>
            </form>
        </>
    );
}
