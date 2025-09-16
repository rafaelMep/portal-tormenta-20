// import {
//     Dialog,
//     DialogPanel,
//     Transition,
//     TransitionChild,
// } from '@headlessui/react';
// import { PropsWithChildren } from 'react';

// export default function Modal({
//     children,
//     show = false,
//     maxWidth = '2xl',
//     closeable = true,
//     onClose = () => {},
// }: PropsWithChildren<{
//     show: boolean;
//     maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl';
//     closeable?: boolean;
//     onClose: CallableFunction;
// }>) {
//     const close = () => {
//         if (closeable) {
//             onClose();
//         }
//     };

//     const maxWidthClass = {
//         sm: 'sm:max-w-sm',
//         md: 'sm:max-w-md',
//         lg: 'sm:max-w-lg',
//         xl: 'sm:max-w-xl',
//         '2xl': 'sm:max-w-2xl',
//     }[maxWidth];

//     return (
//         <Transition show={show} leave="duration-200">
//             <Dialog
//                 as="div"
//                 id="modal"
//                 className="fixed inset-0 z-50 flex transform items-center overflow-y-auto px-4 py-6 transition-all sm:px-0"
//                 onClose={close}
//             >
//                 <TransitionChild
//                     enter="ease-out duration-300"
//                     enterFrom="opacity-0"
//                     enterTo="opacity-100"
//                     leave="ease-in duration-200"
//                     leaveFrom="opacity-100"
//                     leaveTo="opacity-0"
//                 >
//                     <div className="absolute inset-0 bg-gray-500/75" />
//                 </TransitionChild>

//                 <TransitionChild
//                     enter="ease-out duration-300"
//                     enterFrom="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
//                     enterTo="opacity-100 translate-y-0 sm:scale-100"
//                     leave="ease-in duration-200"
//                     leaveFrom="opacity-100 translate-y-0 sm:scale-100"
//                     leaveTo="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
//                 >
//                     <DialogPanel
//                         className={`mb-6 transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:mx-auto sm:w-full ${maxWidthClass}`}
//                     >
//                         {children}
//                     </DialogPanel>
//                 </TransitionChild>
//             </Dialog>
//         </Transition>
//     );
// }

import { ReactNode, useEffect } from 'react';

type Props = {
    open: boolean;
    onClose: () => void;
    title?: string;
    children: ReactNode;
    maxWidth?: 'sm' | 'md' | 'lg';
};

export default function Modal({ open, onClose, title, children, maxWidth = 'md' }: Props) {
    useEffect(() => {
        if (!open) return;
        const onKey = (e: KeyboardEvent) => e.key === 'Escape' && onClose();
        document.addEventListener('keydown', onKey);
        return () => document.removeEventListener('keydown', onKey);
    }, [open, onClose]);

    if (!open) return null;

    const sizes = { sm: 'max-w-sm', md: 'max-w-md', lg: 'max-w-lg' }[maxWidth];

    return (
        <div
            className="fixed inset-0 z-50 flex items-center justify-center p-4"
            aria-modal="true"
            role="dialog"
            onClick={onClose}
        >
            <div className="absolute inset-0 bg-black/70 backdrop-blur-sm" />
            <div
                className={`relative w-full ${sizes} rounded-xl bg-neutral-900 text-white shadow-2xl ring-1 ring-white/10`}
                onClick={(e) => e.stopPropagation()}
            >
                <div className="flex items-center justify-between border-b border-white/10 px-4 py-3">
                    <h3 className="text-base font-semibold">{title}</h3>
                    <button
                        onClick={onClose}
                        aria-label="Fechar"
                        className="rounded-md p-1 text-white/80 hover:text-white hover:bg-white/10"
                    >
                        ✕
                    </button>
                </div>
                <div className="p-4">{children}</div>
            </div>
        </div>
    );
}
