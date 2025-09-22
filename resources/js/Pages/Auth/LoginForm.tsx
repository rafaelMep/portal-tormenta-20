import { FormEventHandler, useEffect } from 'react';
import { Link, useForm } from '@inertiajs/react';
import Checkbox from '@/Components/Checkbox';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';

type Props = {
    initialEmail?: string;
    onSwitchToRegister?: () => void;
    onSwitchToForgot?: () => void;
};

export default function LoginForm({ initialEmail, onSwitchToRegister, onSwitchToForgot }: Props) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    useEffect(() => {
        if (initialEmail) setData('email', initialEmail);
    }, [initialEmail]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), { onFinish: () => reset('password') });
    };

    return (
        <form onSubmit={submit} className="space-y-4">
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
                    isFocused={Boolean(initialEmail)}
                />
                <InputError message={errors.password} className="mt-2" />
            </div>

            <div className="flex items-center justify-between">
                <label className="inline-flex items-center gap-2">
                    <Checkbox
                        name="remember"
                        checked={data.remember}
                        onChange={(e) => setData('remember', e.target.checked)}
                    />
                    <span className="text-sm text-white/80">Lembrar de mim</span>
                </label>

                {onSwitchToForgot ? (
                    <button
                        type="button"
                        onClick={onSwitchToForgot}
                        className="text-sm text-rose-300 hover:text-rose-200 underline underline-offset-2"
                    >
                        Esqueci minha senha
                    </button>
                ) : (
                    <Link href={route('password.request')} className="text-sm text-rose-300 hover:text-rose-200">
                        Esqueci minha senha
                    </Link>
                )}
            </div>

            <PrimaryButton className="w-full justify-center bg-rose-600 hover:bg-rose-500 focus:ring-rose-400" disabled={processing}>
                Entrar
            </PrimaryButton>

            <p className="text-center text-sm text-white/80">
                Não tem conta?{' '}
                {onSwitchToRegister ? (
                    <button
                        type="button"
                        onClick={onSwitchToRegister}
                        className="font-semibold text-rose-300 hover:text-rose-200 underline underline-offset-2"
                    >
                        Criar conta
                    </button>
                ) : (
                    <Link href={route('register')} className="font-semibold text-rose-300 hover:text-rose-200">
                        Criar conta
                    </Link>
                )}
            </p>
        </form>
    );
}
