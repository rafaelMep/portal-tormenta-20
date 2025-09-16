export default function T20Logo({ className = '' }: { className?: string }) {
    return (
        <div className={`flex items-center gap-2 ${className}`}>
            {/* Emblema “d20” bem minimalista */}
            <svg width="28" height="28" viewBox="0 0 24 24" aria-hidden="true"
                className="drop-shadow-sm">
                <defs>
                    <linearGradient id="t20g" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stopColor="#E11D48" />   {/* rose-600 */}
                        <stop offset="100%" stopColor="#7F1D1D" /> {/* red-900 */}
                    </linearGradient>
                </defs>
                <polygon
                    points="12,2 21,7 21,17 12,22 3,17 3,7"
                    fill="url(#t20g)"
                    stroke="#111827"
                    strokeWidth="1.25"
                />
                <text x="12" y="15.25" textAnchor="middle" fontSize="9" fontWeight="700" fill="#fff">
                    20
                </text>
            </svg>
            {/* <span className="font-extrabold tracking-wide text-xl">
                Tormenta <span className="text-rose-500">20</span>
            </span> */}
            <span className="font-extrabold tracking-wide text-xl">
                <span className="mr-1 text-white/80">Portal</span>
                Tormenta <span className="text-rose-500">20</span>
            </span>
        </div>
    );
}
