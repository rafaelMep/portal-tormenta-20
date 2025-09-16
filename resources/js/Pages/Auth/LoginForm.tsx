import { FormEventHandler } from 'react';
import { Link, useForm } from '@inertiajs/react';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import Checkbox from '@/Components/Checkbox';
import PrimaryButton from '@/Components/PrimaryButton';

export default function LoginForm() {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), { onFinish: () => reset('password') });
    };

    return (
        <form onSubmit={submit} className="space-y-4">
            <div>
                <InputLabel htmlFor="email" value="E-mail" className="text-white" />
                <TextInput id="email" type="email" value={data.email} className="mt-1 block w-full"
                    autoComplete="username" onChange={(e) => setData('email', e.target.value)} required />
                <InputError message={errors.email} className="mt-2" />
            </div>
            <div>
                <InputLabel htmlFor="password" value="Senha" className="text-white" />
                <TextInput id="password" type="password" value={data.password} className="mt-1 block w-full"
                    autoComplete="current-password" onChange={(e) => setData('password', e.target.value)} required />
                <InputError message={errors.password} className="mt-2" />
            </div>
            <div className="flex items-center justify-between">
                <label className="inline-flex items-center gap-2">
                    <Checkbox name="remember" checked={data.remember}
                        onChange={(e) => setData('remember', e.target.checked)} />
                    <span className="text-sm text-white/80">Lembrar de mim</span>
                </label>
                <Link href={route('password.request')} className="text-sm text-rose-300 hover:text-rose-200">
                    Esqueci minha senha
                </Link>
            </div>
            <PrimaryButton className="w-full justify-center bg-rose-600 hover:bg-rose-500 focus:ring-rose-400" disabled={processing}>
                Entrar
            </PrimaryButton>
        </form>
    );
}
