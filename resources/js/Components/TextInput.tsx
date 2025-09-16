import {
    forwardRef,
    InputHTMLAttributes,
    useEffect,
    useImperativeHandle,
    useRef,
} from 'react';

export default forwardRef(function TextInput(
    {
        type = 'text',
        className = '',
        isFocused = false,
        ...props
    }: InputHTMLAttributes<HTMLInputElement> & { isFocused?: boolean },
    ref,
) {
    const localRef = useRef<HTMLInputElement>(null);

    useImperativeHandle(ref, () => ({
        focus: () => localRef.current?.focus(),
    }));

    useEffect(() => {
        if (isFocused) localRef.current?.focus();
    }, [isFocused]);

    return (
        <input
            {...props}
            type={type}
            ref={localRef}
            className={[
                // base + aparência
                'block w-full rounded-md shadow-sm',
                'border border-neutral-300',
                'bg-white text-neutral-900 placeholder:text-neutral-500',
                // foco/acessibilidade
                'focus:outline-none focus:ring-2 focus:ring-rose-400 focus:border-rose-400',
                // estados
                'disabled:opacity-50 disabled:cursor-not-allowed',
                className,
            ].join(' ')}
        />
    );
});
