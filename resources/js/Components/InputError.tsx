import { HTMLAttributes } from 'react';

export default function InputError({
    message,
    className = '',
}: {
    message?: string;
    className?: string;
}) {
    if (!message) return null;

    return (
        <p className={['text-sm text-white/90', className].join(' ')}>
            {message}
        </p>
    );
}

