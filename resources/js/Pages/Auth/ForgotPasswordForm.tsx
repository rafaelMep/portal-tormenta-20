import { FormEventHandler, useState } from 'react';
import { useForm } from '@inertiajs/react';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';

type Props = {
    onBackToLogin?: () => void;
};

export default function ForgotPasswordForm({ onBackToLogin }: Props) {
    const { data, setData, post, processing, errors, reset } = useForm({ email: '' });
    const [sent, setSent] = useState<string | null>(null);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        setSent(null);
        post(route('password.email'), {
            onSuccess: () => {
                setSent('Se o e-mail existir, enviamos um link de redefinição.');
                reset('email');
            },
        });
    };

    return (
        <form onSubmit={submit} className="space-y-4">
            <p className="text-white/85 text-sm">
                Informe seu e-mail. Enviaremos um link para redefinir sua senha.
            </p>

            <div>
                <InputLabel htmlFor="reset_email" value="E-mail" className="text-white" />
                <TextInput
                    id="reset_email"
                    type="email"
                    value={data.email}
                    className="mt-1 block w-full"
                    autoComplete="email"
                    onChange={(e) => setData('email', e.target.value)}
                    required
                />
                <InputError message={errors.email} className="mt-2" />
            </div>

            {sent && (
                <div className="rounded-md border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-emerald-200 text-sm">
                    {sent}
                </div>
            )}

            <div className="flex gap-3">
                <PrimaryButton
                    className="justify-center bg-rose-600 hover:bg-rose-500 focus:ring-rose-400"
                    disabled={processing}
                >
                    Enviar link
                </PrimaryButton>

                {onBackToLogin && (
                    <button
                        type="button"
                        onClick={onBackToLogin}
                        className="px-4 py-2 text-sm font-semibold text-white/90 hover:text-white"
                    >
                        Voltar ao login
                    </button>
                )}
            </div>
        </form>
    );
}
