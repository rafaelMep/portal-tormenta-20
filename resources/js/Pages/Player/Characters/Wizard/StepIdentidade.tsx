import React from 'react';
import type { Draft } from '@/domain/tormenta/types';

interface Props {
  draft: Draft;
  setDraft: React.Dispatch<React.SetStateAction<Draft>>;
}

export default function StepIdentidade({ draft, setDraft }: Props) {
  return (
    <div>
      <h2 className="text-xl font-bold">📝 Identidade</h2>
      <p className="text-white/70">Defina o nome e o nível inicial.</p>

      <div className="mt-4 grid sm:grid-cols-2 gap-4">
        <Field label="Nome">
          <input
            value={draft.identidade.nome}
            onChange={(e) =>
              setDraft((d) => ({
                ...d,
                identidade: { ...d.identidade, nome: e.target.value },
              }))
            }
            placeholder="Ex.: Arthel"
            className="w-full rounded-xl bg-black/40 border border-white/10 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-rose-400/40"
          />
        </Field>

        <Field label="Nível">
          <input
            type="number"
            min={1}
            max={20}
            value={draft.identidade.nivel}
            onChange={(e) =>
              setDraft((d) => ({
                ...d,
                identidade: {
                  ...d.identidade,
                  nivel: parseInt(e.target.value || '1'),
                },
              }))
            }
            className="w-full rounded-xl bg-black/40 border border-white/10 px-3 py-2"
          />
        </Field>
      </div>
    </div>
  );
}

function Field({
  label,
  children,
}: {
  label: string;
  children: React.ReactNode;
}) {
  return (
    <label className="block">
      <span className="block text-sm text-white/70 mb-1">{label}</span>
      {children}
    </label>
  );
}
