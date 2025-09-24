import DashboardLayout from '@/Layouts/DashboardLayout';

export default function Create() {
    return (
        <DashboardLayout>
            <h1 className="text-2xl font-bold">Nova ficha</h1>

            <form className="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <label className="space-y-1">
                    <span className="text-sm text-white/80">Nome do personagem</span>
                    <input
                        className="w-full rounded-md bg-white text-black px-3 py-2"
                        placeholder="Ex.: Sir Bartholomeu"
                    />
                </label>

                <label className="space-y-1">
                    <span className="text-sm text-white/80">Classe</span>
                    <input className="w-full rounded-md bg-white text-black px-3 py-2" placeholder="Ex.: Guerreiro" />
                </label>

                <label className="space-y-1">
                    <span className="text-sm text-white/80">Nível</span>
                    <input type="number" min={1} max={20} defaultValue={1}
                        className="w-full rounded-md bg-white text-black px-3 py-2" />
                </label>

                {/* futuro: Raça, Caminho, Divindade, Atributos, etc. */}

                <div className="md:col-span-2 mt-4">
                    <button
                        type="button"
                        className="rounded-lg px-5 py-2 bg-rose-600 hover:bg-rose-500 ring-1 ring-rose-400/50"
                    >
                        Salvar (placeholder)
                    </button>
                </div>
            </form>
        </DashboardLayout>
    );
}
