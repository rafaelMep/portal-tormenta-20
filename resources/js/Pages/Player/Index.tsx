import DashboardLayout from '@/Layouts/DashboardLayout';
import FeatureCard from '@/Components/FeatureCard';
import { ScrollText, MapPinned } from 'lucide-react';

export default function PlayerHome() {
    return (
        <DashboardLayout>
            <h1 className="text-2xl font-bold">Jogador</h1>
            <p className="mt-1 text-white/70">Escolha uma ferramenta para começar.</p>

            <div className="mt-6 grid gap-6 sm:grid-cols-2">
                <FeatureCard
                    title="Fichas de personagem"
                    subtitle="Crie e gerencie suas fichas (nível 1–20)."
                    href={route('dashboard.player.characters.index')}
                    accent="rose"
                    icon={<ScrollText className="h-5 w-5" />}
                    cta="Abrir fichas"
                    overlay="hard"
                />

                <FeatureCard
                    title="Campanha"
                    subtitle="Acompanhe sessões, anotações e progresso."
                    href={route('dashboard.player.campaign.index')}
                    accent="amber"
                    icon={<MapPinned className="h-5 w-5" />}
                    cta="Abrir campanha"
                    overlay="hard"
                />
            </div>
        </DashboardLayout>
    );
}
