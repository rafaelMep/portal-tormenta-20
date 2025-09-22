import { FormEventHandler } from 'react';
import { useForm } from '@inertiajs/react';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';

type Props = {
    token: string;
    email?: string | null;
    onBackToLogin?: () => void;
};

export default function ResetPasswordForm({ token, email, onBackToLogin }: Props) {
    const { data, setData, post, processing, errors, reset } = useForm({
        token,
        email: email ?? '',
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('password.store'), {
            onSuccess: () => reset('password', 'password_confirmation'),
        });
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
                    autoComplete="email"
                    onChange={(e) => setData('email', e.target.value)}
                    required
                />
                <InputError message={errors.email} className="mt-2" />
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <InputLabel htmlFor="password" value="Nova senha" className="text-white" />
                    <TextInput
                        id="password"
                        type="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(e) => setData('password', e.target.value)}
                        required
                    />
                </div>
                <div>
                    <InputLabel htmlFor="password_confirmation" value="Confirmar nova senha" className="text-white" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        value={data.password_confirmation}
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        required
                    />
                </div>
                <div className="sm:col-span-2">
                    <InputError message={errors.password} className="mt-2" />
                </div>
            </div>

            <PrimaryButton
                className="w-full justify-center bg-rose-600 hover:bg-rose-500 focus:ring-rose-400"
                disabled={processing}
            >
                Redefinir senha
            </PrimaryButton>

            {onBackToLogin && (
                <p className="text-center text-sm text-white/80 mt-1">
                    Lembrou a senha?{' '}
                    <button
                        type="button"
                        onClick={onBackToLogin}
                        className="font-semibold text-rose-300 hover:text-rose-200 underline underline-offset-2"
                    >
                        Voltar ao login
                    </button>
                </p>
            )}
        </form>
    );
}
