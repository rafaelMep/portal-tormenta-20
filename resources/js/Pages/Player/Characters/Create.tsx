import React, { useEffect, useMemo, useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';

// domínio / regras
import type { Attr, Draft, Race, RaceVariant, RaceChoiceGroup, Skill } from '@/domain/tormenta/types';
import { ATTRS, labelAttr, mod } from '@/domain/tormenta/attributes';
import {
  initialDraft,
  sumPointBuy,
  gatherRacialMods,
  applyFinal,
} from '@/domain/tormenta/calculators';

import StepIdentidade from '@/Pages/Player/Characters/Wizard/StepIdentidade';

/** ======================= MOCKS (só se o backend não enviar nada) ======================= */
const MOCK_RACES: Race[] = [
  {
    id: 1,
    slug: 'humano',
    name: 'Humano',
    size: 'Médio',
    speed: 9,
    summary: '+1 em três atributos distintos. Versátil. (Exemplo minimal)',
    choice_sets: [
      {
        id: 11,
        race_id: 1,
        key: 'humano_plus1_x3',
        label: '+1 em 3 atributos distintos',
        min_picks: 3,
        max_picks: 3,
        options: ATTRS.map((a) => ({
          id: 0,
          set_id: 11,
          value: a,
          label: labelAttr(a),
        })),
      },
    ],
  },
  {
    id: 2,
    slug: 'anao',
    name: 'Anão',
    size: 'Médio',
    speed: 6,
    summary: 'CON+2, SAB+1, DES-1; deslocamento não reduz por armadura/carga.',
    attribute_mods: [
      { id: 0, mode: 'fixed', attribute: 'CON', modifier: +2 },
      { id: 0, mode: 'fixed', attribute: 'SAB', modifier: +1 },
      { id: 0, mode: 'fixed', attribute: 'DES', modifier: -1 },
    ],
  },
  {
    id: 3,
    slug: 'suraggel',
    name: 'Suraggel',
    size: 'Médio',
    speed: 9,
    summary: 'Variante Aggelus (SAB+2, CAR+1) ou Sulfure (DES+2, INT+1).',
    variants: [
      {
        id: 31,
        race_id: 3,
        key: 'aggelus',
        name: 'Aggelus',
        attribute_mods: [
          { id: 0, mode: 'fixed', attribute: 'SAB', modifier: +2 },
          { id: 0, mode: 'fixed', attribute: 'CAR', modifier: +1 },
        ],
      },
      {
        id: 32,
        race_id: 3,
        key: 'sulfure',
        name: 'Sulfure',
        attribute_mods: [
          { id: 0, mode: 'fixed', attribute: 'DES', modifier: +2 },
          { id: 0, mode: 'fixed', attribute: 'INT', modifier: +1 },
        ],
      },
    ],
  },
];

const MOCK_SKILLS: Skill[] = [
  { id: 1, slug: 'percepcao', name: 'Percepção', key_attr: 'SAB' },
  { id: 2, slug: 'atletismo', name: 'Atletismo', key_attr: 'FOR' },
  { id: 3, slug: 'furtividade', name: 'Furtividade', key_attr: 'DES' },
  { id: 4, slug: 'misticismo', name: 'Misticismo', key_attr: 'INT' },
  { id: 5, slug: 'intimidacao', name: 'Intimidação', key_attr: 'CAR' },
];

/** ======================= COMPONENTE PRINCIPAL ======================= */
export default function CreateCharacter() {
  const { props } = usePage() as any;

  // backend manda shape canônico; se não vier, cai no mock
  const races: Race[] = props.races?.length ? props.races : (MOCK_RACES as any);

  const rawSkills = props.skills?.length ? props.skills : MOCK_SKILLS;
  const skills: Skill[] = useMemo(
    () =>
      rawSkills.map((s: any) => ({
        ...s,
        slug: s.slug ?? s.code ?? String(s.id ?? '').toLowerCase(),
        name: s.name ?? s.title ?? s.slug ?? 'Perícia',
        key_attr: (s.key_attr ?? s.ability_abbr ?? s.ability ?? 'SAB') as Attr,
      })),
    [rawSkills]
  );

  const [draft, setDraft] = useState<Draft>(() => initialDraft(skills));
  const [step, setStep] = useState(0);

  // raça/variante selecionadas
  const race = useMemo(
    () => races.find((r) => r.id === draft.raca.id) ?? null,
    [races, draft.raca.id]
  );

  const variant = useMemo(
    () => race?.variants?.find((v) => v.id === draft.raca.variant_id) ?? null,
    [race, draft.raca.variant_id]
  );

  // mods raciais (agora só chama o domínio)
  const racialMods = useMemo(
    () =>
      gatherRacialMods(
        race,
        variant,
        draft.raca.set_picks,
        draft.raca.group_picks,
        race?.choice_groups ?? []
      ),
    [race, variant, draft.raca.set_picks, draft.raca.group_picks]
  );

  const gastos = useMemo(() => sumPointBuy(draft.atributos.base), [draft.atributos.base]);
  const restantes = draft.atributos.pontos - gastos;

  const finalAttrs = useMemo(
    () => applyFinal(draft.atributos.base, racialMods),
    [draft.atributos.base, racialMods]
  );

  // autosave simples no front
  useEffect(() => {
    localStorage.setItem('t20-wizard-draft', JSON.stringify(draft));
  }, [draft]);

  useEffect(() => {
    const saved = localStorage.getItem('t20-wizard-draft');
    if (saved) {
      try {
        setDraft(JSON.parse(saved));
      } catch {}
    }
  }, []);

  return (
    <DashboardLayout>
      <Head title="Criar Personagem" />

      <div className="no-print">
        <h1 className="text-2xl font-bold">Criar personagem</h1>
        <p className="text-white/70">Um passo a passo rápido para montar sua ficha.</p>
      </div>

      <Stepper
        current={step}
        onGo={setStep}
        items={[
          { icon: '📝', label: 'Identidade' },
          { icon: '🧬', label: 'Raça' },
          { icon: '🧠', label: 'Atributos' },
          { icon: '📚', label: 'Perícias' },
          { icon: '✅', label: 'Revisão' },
        ]}
      />

      <div className="mt-6 grid gap-6 lg:grid-cols-[3fr,2fr]">
        <section className="rounded-2xl border border-white/10 bg-white/[0.04] p-5 print-area">
          {step === 0 && <StepIdentidade draft={draft} setDraft={setDraft} />}

          {step === 1 && (
            <StepRaca draft={draft} setDraft={setDraft} races={races} />
          )}

          {step === 2 && (
            <StepAtributos
              draft={draft}
              setDraft={setDraft}
              restantes={restantes}
              racialMods={racialMods}
              finalAttrs={finalAttrs}
            />
          )}

          {step === 3 && (
            <StepPericias
              draft={draft}
              setDraft={setDraft}
              finalAttrs={finalAttrs}
            />
          )}

          {step === 4 && (
            <StepRevisao
              draft={draft}
              race={race}
              variant={variant}
              finalAttrs={finalAttrs}
            />
          )}

          <div className="mt-5 flex gap-2 no-print">
            {step > 0 && (
              <button
                onClick={() => setStep((s) => s - 1)}
                className="rounded-xl px-4 py-2 bg-white/10 hover:bg-white/15 ring-1 ring-white/10"
              >
                Voltar
              </button>
            )}

            {step < 4 && (
              <button
                onClick={() => setStep((s) => s + 1)}
                className="rounded-xl px-4 py-2 bg-rose-600 hover:bg-rose-500 ring-1 ring-rose-400/40"
              >
                Continuar
              </button>
            )}

            {step === 4 && (
              <div className="ml-auto flex gap-2">
                <button
                  onClick={() => window.print()}
                  className="rounded-xl px-4 py-2 bg-emerald-600 hover:bg-emerald-500 ring-1 ring-emerald-400/40"
                >
                  Imprimir / Salvar PDF
                </button>
                <button
                  onClick={() => downloadJSON(draft)}
                  className="rounded-xl px-4 py-2 bg-sky-600 hover:bg-sky-500 ring-1 ring-sky-400/40"
                >
                  Baixar JSON
                </button>
              </div>
            )}
          </div>
        </section>

        <aside className="rounded-2xl border border-white/10 bg-white/[0.02] p-5 h-fit no-print">
          <ResumoLateral
            draft={draft}
            race={race}
            variant={variant}
            restantes={restantes}
            finalAttrs={finalAttrs}
          />
        </aside>
      </div>

      <style>{`
        @media print {
          header, nav, .no-print { display: none !important; }
          html, body { background: #fff !important; }
          .print-area { background: #fff !important; color: #000 !important; border: none !important; box-shadow:none !important; }
          .print-box { break-inside: avoid; page-break-inside: avoid; }
        }
      `}</style>
    </DashboardLayout>
  );
}

/** ======================= STEP 2 — RAÇA ======================= */
function StepRaca({
  draft,
  setDraft,
  races,
}: {
  draft: Draft;
  setDraft: React.Dispatch<React.SetStateAction<Draft>>;
  races: Race[];
}) {
  const [q, setQ] = useState('');
  const [expanded, setExpanded] = useState(false);

  const hasQuery = q.trim().length > 0;

  const filtered = useMemo(() => {
    if (!hasQuery) return [] as Race[];
    const t = q.trim().toLowerCase();
    return races.filter((r) =>
      `${r.name} ${r.slug} ${r.summary ?? ''}`.toLowerCase().includes(t)
    );
  }, [q, races, hasQuery]);

  const visible = useMemo(
    () => (expanded ? filtered : filtered.slice(0, 3)),
    [filtered, expanded]
  );

  useEffect(() => setExpanded(false), [q]);

  const selected = useMemo(
    () => races.find((r) => r.id === draft.raca.id) ?? null,
    [races, draft.raca.id]
  );

  const pickRace = (r: Race) =>
    setDraft((d) => ({
      ...d,
      raca: {
        id: r.id,
        slug: r.slug,
        name: r.name,
        variant_id: null,
        variant_key: null,
        set_picks: [],
        group_picks: [],
      },
    }));

  const toggleSetPick = (setId: number, key: string, value: Attr, max: number) =>
    setDraft((d) => {
      const existing = d.raca.set_picks.find((s) => s.set_id === setId);
      let nextPicks = existing?.picks ?? [];

      if (nextPicks.includes(value)) nextPicks = nextPicks.filter((v) => v !== value);
      else if (nextPicks.length < max) nextPicks = [...nextPicks, value];

      const rest = d.raca.set_picks.filter((s) => s.set_id !== setId);
      return {
        ...d,
        raca: { ...d.raca, set_picks: [...rest, { set_id: setId, key, picks: nextPicks }] },
      };
    });

  const toggleGroupPick = (group: RaceChoiceGroup, optionId: number) =>
    setDraft((d) => {
      const cur = d.raca.group_picks.find((g) => g.group_id === group.id);
      let selectedIds = cur?.option_ids ?? [];
      const has = selectedIds.includes(optionId);

      if (group.max_choices === 1) {
        selectedIds = has ? [] : [optionId];
      } else {
        if (has) selectedIds = selectedIds.filter((id) => id !== optionId);
        else selectedIds = [...selectedIds, optionId].slice(0, group.max_choices);
      }

      const rest = d.raca.group_picks.filter((g) => g.group_id !== group.id);
      return {
        ...d,
        raca: { ...d.raca, group_picks: [...rest, { group_id: group.id, option_ids: selectedIds }] },
      };
    });

  return (
    <div>
      <h2 className="text-xl font-bold">🧬 Raça</h2>
      <p className="text-white/70">
        Busque pelo nome. Os resultados aparecem quando você começa a digitar.
      </p>

      <div className="mt-3 flex items-center gap-2">
        <input
          value={q}
          onChange={(e) => setQ(e.target.value)}
          placeholder="Buscar raça..."
          className="w-full rounded-xl bg-black/40 border border-white/10 px-3 py-2"
        />
        <span className="text-xs text-white/60">
          {hasQuery ? `${filtered.length} itens` : '—'}
        </span>
      </div>

      {hasQuery && (
        <>
          <div className="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            {visible.map((r) => (
              <RaceCard
                key={r.id}
                race={r}
                selected={draft.raca.id === r.id}
                onSelect={() => pickRace(r)}
              />
            ))}
          </div>

          {filtered.length > 3 && (
            <div className="mt-3">
              <button
                onClick={() => setExpanded((v) => !v)}
                className="rounded-xl px-4 py-2 bg-white/10 hover:bg-white/15 ring-1 ring-white/10"
              >
                {expanded ? 'Mostrar menos' : `Mostrar mais (${filtered.length - 3} restantes)`}
              </button>
            </div>
          )}

          {filtered.length === 0 && (
            <div className="mt-4 text-white/60 text-sm">
              Nenhuma raça encontrada para “{q}”.
            </div>
          )}
        </>
      )}

      {selected && (
        <div className="mt-6 rounded-2xl border border-white/10 bg-white/[0.02] p-4 space-y-5">
          {!!selected.variants?.length && (
            <div>
              <div className="text-sm text-white/70 mb-2">Variante</div>
              <div className="flex flex-wrap gap-2">
                {selected.variants.map((v) => (
                  <button
                    key={v.id}
                    onClick={() =>
                      setDraft((d) => ({
                        ...d,
                        raca: { ...d.raca, variant_id: v.id, variant_key: v.key },
                      }))
                    }
                    className={`rounded-xl px-3 py-1.5 text-sm ring-1 transition ${
                      draft.raca.variant_id === v.id
                        ? 'bg-rose-600/20 text-rose-200 ring-rose-400/40'
                        : 'bg-white/10 ring-white/10 hover:bg-white/15'
                    }`}
                  >
                    {v.name}
                  </button>
                ))}
              </div>
              {selected.variants.find((v) => v.id === draft.raca.variant_id)?.summary && (
                <p className="mt-2 text-sm text-white/70">
                  {selected.variants.find((v) => v.id === draft.raca.variant_id)?.summary}
                </p>
              )}
            </div>
          )}

          {!!selected.choice_sets?.length && (
            <div id="race-sets">
              <div className="text-sm text-white/70 mb-2">Bônus por escolha</div>
              <div className="grid gap-3">
                {selected.choice_sets.map((set) => {
                  const pickedCount =
                    draft.raca.set_picks.find((s) => s.set_id === set.id)?.picks.length ?? 0;
                  const excludes: Attr[] = (set.constraints?.exclude ?? []) as Attr[];

                  return (
                    <div key={set.id} className="rounded-xl border border-white/10 bg-white/[0.03] p-3">
                      <div className="flex items-center justify-between gap-2">
                        <div className="font-medium">{set.label || set.key}</div>
                        <div className="text-xs text-white/60">
                          {pickedCount}/{set.max_picks}
                        </div>
                      </div>

                      <div className="mt-2 flex flex-wrap gap-2">
                        {set.options.map((opt) => {
                          const val = opt.value as Attr;
                          const disabled = excludes.includes(val);

                          const picked =
                            !!draft.raca.set_picks
                              .find((s) => s.set_id === set.id)
                              ?.picks.includes(val);

                          const cap =
                            (draft.raca.set_picks.find((s) => s.set_id === set.id)?.picks.length ??
                              0) >= set.max_picks && !picked;

                          return (
                            <button
                              key={`${set.id}-${val}`}
                              onClick={() =>
                                !disabled && toggleSetPick(set.id, set.key, val, set.max_picks)
                              }
                              disabled={disabled || cap}
                              className={`rounded-lg px-2.5 py-1 text-sm ring-1 transition
                                ${
                                  disabled
                                    ? 'opacity-40 cursor-not-allowed ring-white/10'
                                    : picked
                                    ? 'bg-emerald-600/20 ring-emerald-400/40 text-emerald-100'
                                    : 'bg-white/10 ring-white/10 hover:bg-white/15'
                                }`}
                            >
                              +1 {labelAttr(val)}
                            </button>
                          );
                        })}
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
          )}

          {!!selected.choice_groups?.length && (
            <div>
              <div className="text-sm text-white/70 mb-2">Escolhas da Raça</div>
              <div className="space-y-3">
                {selected.choice_groups
                  .sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0))
                  .map((group) => {
                    const asRadio = group.max_choices === 1;
                    const pickedIds =
                      draft.raca.group_picks.find((g) => g.group_id === group.id)?.option_ids ??
                      [];

                    return (
                      <div key={group.id} className="rounded-xl border border-white/10 bg-white/[0.03] p-3">
                        <div className="text-sm text-white/80 mb-2">
                          <span className="font-medium">{group.name}</span>{' '}
                          <span className="text-white/60">
                            {group.required ? '(obrigatório)' : '(opcional)'} — escolha{' '}
                            {group.min_choices === group.max_choices
                              ? group.max_choices
                              : `${group.min_choices}-${group.max_choices}`}
                          </span>
                        </div>

                        <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                          {group.options.map((opt) => {
                            const active = pickedIds.includes(opt.id);

                            return (
                              <label
                                key={opt.id}
                                className={`cursor-pointer rounded-xl border p-3 transition
                                  ${
                                    active
                                      ? 'border-rose-400/40 bg-rose-600/15'
                                      : 'border-white/10 bg-white/5 hover:bg-white/8'
                                  }`}
                              >
                                <div className="flex items-start justify-between gap-3">
                                  <div>
                                    <div className="text-sm font-semibold">{opt.name ?? opt.key}</div>
                                    {opt.summary && (
                                      <p className="mt-1 text-xs text-white/70">{opt.summary}</p>
                                    )}
                                  </div>

                                  <input
                                    type={asRadio ? 'radio' : 'checkbox'}
                                    className="mt-1"
                                    checked={active}
                                    onChange={() => toggleGroupPick(group, opt.id)}
                                  />
                                </div>
                              </label>
                            );
                          })}
                        </div>
                      </div>
                    );
                  })}
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  );
}

/** ======================= STEP 3 — ATRIBUTOS ======================= */
function StepAtributos({
  draft,
  setDraft,
  restantes,
  racialMods,
  finalAttrs,
}: {
  draft: Draft;
  setDraft: React.Dispatch<React.SetStateAction<Draft>>;
  restantes: number;
  racialMods: { attribute: Attr; modifier: number; source: string }[];
  finalAttrs: Record<Attr, number>;
}) {
  const inc = (a: Attr) =>
    setDraft((d) => ({
      ...d,
      atributos: {
        ...d.atributos,
        base: { ...d.atributos.base, [a]: Math.min(18, d.atributos.base[a] + 1) },
      },
    }));

  const dec = (a: Attr) =>
    setDraft((d) => ({
      ...d,
      atributos: {
        ...d.atributos,
        base: { ...d.atributos.base, [a]: Math.max(8, d.atributos.base[a] - 1) },
      },
    }));

  return (
    <div>
      <h2 className="text-xl font-bold">🧠 Atributos</h2>
      <p className="text-white/70">
        Distribua {draft.atributos.pontos} pontos. Os modificadores raciais são aplicados automaticamente.
      </p>

      <div className="mt-4 grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
        {ATTRS.map((a) => (
          <div key={a} className="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
            <div className="flex items-center justify-between">
              <div className="text-sm text-white/70">{labelAttr(a)}</div>
              <div className="text-white/60 text-sm">
                mod {mod(finalAttrs[a]) >= 0 ? '+' : ''}
                {mod(finalAttrs[a])}
              </div>
            </div>

            <div className="mt-2 flex items-center gap-2">
              <button onClick={() => dec(a)} className="rounded-lg px-2 py-1 bg-white/10 hover:bg-white/15">
                –
              </button>

              <div className="text-2xl font-bold tabular-nums">{draft.atributos.base[a]}</div>

              <button
                onClick={() => inc(a)}
                disabled={restantes <= 0}
                className="rounded-lg px-2 py-1 bg-white/10 hover:bg-white/15 disabled:opacity-50"
              >
                +
              </button>

              <div className="ml-auto text-sm text-white/60">
                final <span className="font-semibold">{finalAttrs[a]}</span>
              </div>
            </div>
          </div>
        ))}
      </div>

      <div className="mt-3 flex flex-wrap items-center gap-2">
        <Badge>Gastos: {draft.atributos.pontos - Math.max(0, restantes)}</Badge>
        <Badge accent={restantes < 0 ? 'rose' : 'sky'}>Restantes: {restantes}</Badge>
      </div>

      {!!racialMods.length && (
        <div className="mt-5 rounded-2xl border border-white/10 bg-white/[0.02] p-4">
          <div className="text-sm text-white/70 mb-2">Modificadores Raciais</div>
          <div className="flex flex-wrap gap-2">
            {racialMods.map((m, i) => (
              <span key={i} className="rounded-lg bg-black/40 px-2 py-1 text-sm">
                {m.attribute} {m.modifier > 0 ? '+' : ''}
                {m.modifier} <span className="text-white/50">· {m.source}</span>
              </span>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}

/** ======================= STEP 4 — PERÍCIAS ======================= */
function StepPericias({
  draft,
  setDraft,
  finalAttrs,
}: {
  draft: Draft;
  setDraft: React.Dispatch<React.SetStateAction<Draft>>;
  finalAttrs: Record<Attr, number>;
}) {
  const entries = Object.entries(draft.pericias);

  const toggle = (slug: string) =>
    setDraft((d) => ({
      ...d,
      pericias: {
        ...d.pericias,
        [slug]: { ...d.pericias[slug], treinado: !d.pericias[slug].treinado },
      },
    }));

  const setBonus = (slug: string, v: number) =>
    setDraft((d) => ({
      ...d,
      pericias: {
        ...d.pericias,
        [slug]: { ...d.pericias[slug], bonus: v },
      },
    }));

  return (
    <div>
      <h2 className="text-xl font-bold">📚 Perícias</h2>
      <p className="text-white/70">Marque as treinadas e ajuste bônus situacionais (se quiser).</p>

      <div className="mt-3 grid gap-2">
        {entries.map(([slug, s]) => (
          <div key={slug} className="grid grid-cols-[1fr,110px,110px] items-center gap-3 rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2">
            <label className="flex items-center gap-2">
              <input type="checkbox" checked={s.treinado} onChange={() => toggle(slug)} />
              <span className="font-medium">{s.name}</span>
              <span className="text-xs text-white/60">({s.key_attr})</span>
            </label>

            <div className="text-sm text-white/80">
              mod {mod(finalAttrs[s.key_attr]) >= 0 ? '+' : ''}
              {mod(finalAttrs[s.key_attr])}
            </div>

            <input
              type="number"
              className="w-full rounded-lg bg-black/40 border border-white/10 px-2 py-1"
              value={s.bonus}
              onChange={(e) => setBonus(slug, parseInt(e.target.value || '0'))}
            />
          </div>
        ))}
      </div>
    </div>
  );
}

/** ======================= STEP 5 — REVISÃO / IMPRESSÃO ======================= */
function StepRevisao({
  draft,
  race,
  variant,
  finalAttrs,
}: {
  draft: Draft;
  race: Race | null;
  variant: RaceVariant | null;
  finalAttrs: Record<Attr, number>;
}) {
  return (
    <div>
      <h2 className="text-xl font-bold no-print">✅ Revisão</h2>
      <p className="text-white/70 no-print">Revise os dados. Na impressão, apenas a ficha abaixo será exibida.</p>

      <div className="mt-4 grid gap-4 print-grid">
        <div className="print-box rounded-2xl border border-white/10 bg-white/[0.03] p-4">
          <div className="flex flex-wrap items-end justify-between gap-4">
            <div>
              <div className="text-sm text-white/60">Personagem</div>
              <div className="text-2xl font-extrabold">{draft.identidade.nome || '—'}</div>
            </div>

            <div className="text-right">
              <div className="text-sm text-white/60">Raça / Variante</div>
              <div className="font-semibold">
                {race?.name ?? '—'} {variant ? `· ${variant.name}` : ''}
              </div>
            </div>
          </div>
        </div>

        <div className="print-box rounded-2xl border border-white/10 bg-white/[0.03] p-4">
          <div className="font-semibold mb-2">Atributos</div>
          <div className="grid grid-cols-3 sm:grid-cols-6 gap-2">
            {ATTRS.map((a) => (
              <div key={a} className="rounded-xl bg-black/40 border border-white/10 p-2 text-center">
                <div className="text-[11px] text-white/60">{labelAttr(a)}</div>
                <div className="text-2xl font-extrabold tabular-nums">{finalAttrs[a]}</div>
                <div className="text-xs text-white/60">
                  mod {mod(finalAttrs[a]) >= 0 ? '+' : ''}
                  {mod(finalAttrs[a])}
                </div>
              </div>
            ))}
          </div>
        </div>

        <div className="print-box rounded-2xl border border-white/10 bg-white/[0.03] p-4">
          <div className="font-semibold mb-2">Perícias</div>
          <div className="grid sm:grid-cols-2 gap-2">
            {Object.entries(draft.pericias).map(([slug, s]) => (
              <div key={slug} className="flex items-center justify-between rounded-lg bg-black/30 border border-white/10 px-2 py-1">
                <div className="truncate">
                  <span className="font-medium">{s.name}</span>
                  {s.treinado && (
                    <span className="ml-1 text:[11px] px-1 rounded bg-emerald-600/30">T</span>
                  )}
                </div>

                <div className="text-sm text-white/70">
                  mod {mod(finalAttrs[s.key_attr]) >= 0 ? '+' : ''}
                  {mod(finalAttrs[s.key_attr])}
                  {s.bonus
                    ? ` · aj ${s.bonus >= 0 ? '+' : ''}${s.bonus}`
                    : ''}
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

/** ======================= RESUMO LATERAL ======================= */
function ResumoLateral({
  draft,
  race,
  variant,
  restantes,
  finalAttrs,
}: {
  draft: Draft;
  race: Race | null;
  variant: RaceVariant | null;
  restantes: number;
  finalAttrs: Record<Attr, number>;
}) {
  return (
    <div>
      <h3 className="text-lg font-bold">Resumo</h3>

      <div className="mt-3 grid gap-2">
        <KV title="Nome" value={draft.identidade.nome || '—'} />
        <KV title="Raça" value={(race?.name ?? '—') + (variant ? ` · ${variant.name}` : '')} />
        <KV title="Pontos restantes" value={String(restantes)} />

        <div className="rounded-xl bg-white/[0.03] border border-white/10 p-3">
          <div className="text-sm text-white/60 mb-2">Atributos finais</div>
          <div className="grid grid-cols-3 gap-1 text-sm">
            {ATTRS.map((a) => (
              <div key={a} className="rounded bg-black/40 px-2 py-1 flex items-center justify-between">
                <span>{a}</span>
                <span className="font-semibold tabular-nums">{finalAttrs[a]}</span>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

/** ======================= UI PRIMITIVES ======================= */
function Stepper({
  items,
  current,
  onGo,
}: {
  items: { icon: string; label: string }[];
  current: number;
  onGo: (n: number) => void;
}) {
  return (
    <div className="no-print mt-4 rounded-2xl border border-white/10 bg-white/[0.03] p-3">
      <div className="grid sm:grid-cols-5 gap-2">
        {items.map((it, i) => (
          <button
            key={i}
            onClick={() => onGo(i)}
            className={`group rounded-xl px-3 py-2 text-left transition ${
              i <= current
                ? 'bg-rose-600/15 ring-1 ring-rose-400/40'
                : 'bg-white/5 ring-1 ring-white/10 hover:bg-white/10'
            }`}
          >
            <div className="flex items-center gap-2">
              <span className="text-lg">{it.icon}</span>
              <div>
                <div className="text-[11px] uppercase tracking-wide text-white/60">
                  Passo {i + 1}
                </div>
                <div className="font-semibold">{it.label}</div>
              </div>
            </div>
          </button>
        ))}
      </div>
    </div>
  );
}

/** ======================= CARD COMPONENT — RACE ======================= */
function RaceCard({
  race,
  selected,
  onSelect,
}: {
  race: Race;
  selected: boolean;
  onSelect: () => void;
}) {
  const [openTip, setOpenTip] = useState(false);
  const [isClamped, setIsClamped] = useState(false);
  const refSum = React.useRef<HTMLParagraphElement>(null);

  const chips: { label: string }[] = [];
  if (race.size) chips.push({ label: race.size });
  if (typeof race.speed === 'number') chips.push({ label: `${race.speed}m` });
  if (race.creature_type) chips.push({ label: race.creature_type });

  useEffect(() => {
    if (!openTip) return;
    const handler = (e: MouseEvent) => {
      const t = e.target as HTMLElement;
      if (!t.closest?.('[data-tip-container]')) setOpenTip(false);
    };
    document.addEventListener('click', handler);
    return () => document.removeEventListener('click', handler);
  }, [openTip]);

  useEffect(() => {
    const check = () => {
      const el = refSum.current;
      if (!el) return;
      setIsClamped(el.scrollHeight - 1 > el.clientHeight);
    };
    check();

    let ro: ResizeObserver | null = null;
    if (typeof ResizeObserver !== 'undefined' && refSum.current) {
      ro = new ResizeObserver(() => check());
      ro.observe(refSum.current);
    } else {
      const onResize = () => check();
      window.addEventListener('resize', onResize);
      return () => window.removeEventListener('resize', onResize);
    }
    return () => {
      if (ro && refSum.current) ro.unobserve(refSum.current);
    };
  }, [race.summary]);

  return (
    <div
      className={[
        'group relative text-left rounded-2xl border p-4 transition',
        'bg-white/[0.03] border-white/10 hover:bg-white/[0.06] hover:border-white/20',
        'focus-within:outline-none focus-within:ring-2 focus-within:ring-rose-400/50',
        selected ? 'ring-2 ring-rose-400/60 bg-rose-600/10 border-transparent' : '',
        'shadow-sm hover:shadow-md',
      ].join(' ')}
    >
      <div className="pointer-events-none absolute inset-x-0 -top-px h-px bg-gradient-to-r from-transparent via-white/20 to-transparent opacity-50" />

      <div className="flex items-start justify-between gap-3">
        <div className="min-w-0">
          <div className="text-lg font-semibold truncate">{race.name}</div>
          <div className="mt-1 flex flex-wrap items-center gap-1.5">
            {chips.map((c, i) => (
              <span
                key={i}
                className="rounded-full bg-black/40 border border-white/10 px-2 py-0.5 text-[11px] uppercase tracking-wide text-white/70"
              >
                {c.label}
              </span>
            ))}
          </div>
        </div>

        <button
          onClick={onSelect}
          className={`mt-0.5 h-7 w-7 rounded-full grid place-items-center transition ${
            selected
              ? 'bg-rose-500/80 ring-1 ring-rose-300/50'
              : 'bg-white/10 ring-1 ring-white/10 hover:bg-white/15'
          }`}
          title={selected ? 'Selecionada' : 'Selecionar'}
        >
          {selected ? (
            <svg viewBox="0 0 20 20" className="h-4 w-4 text-white">
              <path
                fill="currentColor"
                d="M7.6 13.2l-3-3L3 11.8l4.6 4.6L17 7.9l-1.6-1.6z"
              />
            </svg>
          ) : (
            <svg viewBox="0 0 20 20" className="h-4 w-4 text-white/70">
              <circle
                cx="10"
                cy="10"
                r="7"
                fill="none"
                stroke="currentColor"
                strokeWidth="1.5"
              />
            </svg>
          )}
        </button>
      </div>

      {race.summary && (
        <div className="mt-3 relative" data-tip-container>
          <p ref={refSum} className="text-sm text-white/75 line-clamp-3">
            {race.summary}
          </p>

          {isClamped && (
            <>
              <button
                type="button"
                className="mt-1 text-xs text-rose-200/90 hover:text-rose-200 underline underline-offset-2"
                onClick={() => setOpenTip((v) => !v)}
                aria-expanded={openTip}
              >
                {openTip ? 'Fechar' : 'Ler mais'}
              </button>

              {openTip && (
                <div
                  className="absolute right-0 top-full z-20 mt-2 w-80 rounded-xl border border-white/10 bg-black/90 backdrop-blur p-3 shadow-xl"
                  role="dialog"
                >
                  <div className="text-[11px] uppercase tracking-wide text-white/50 mb-1">
                    Resumo completo
                  </div>
                  <div className="text-sm text-white/90 whitespace-pre-wrap">
                    {race.summary}
                  </div>
                </div>
              )}
            </>
          )}
        </div>
      )}

      <div className="mt-3 flex items-center justify-between">
        <span className="text-[11px] text-white/50 uppercase tracking-wide">
          {race.variants?.length
            ? `${race.variants.length} variante${race.variants.length > 1 ? 's' : ''}`
            : 'Sem variantes'}
        </span>

        <span className={`text-xs ${selected ? 'text-rose-200' : 'text-white/60'}`}>
          {selected ? 'Selecionada' : 'Selecionar'}
        </span>
      </div>
    </div>
  );
}

function Badge({
  children,
  accent,
}: {
  children: React.ReactNode;
  accent?: 'sky' | 'rose';
}) {
  const cls =
    accent === 'rose'
      ? 'bg-rose-600/20 ring-rose-400/40 text-rose-100'
      : 'bg-sky-600/20 ring-sky-400/40 text-sky-100';

  return <span className={`rounded-lg px-2 py-1 text-sm ring-1 ${cls}`}>{children}</span>;
}

function KV({ title, value }: { title: string; value: string }) {
  return (
    <div className="rounded-xl bg-white/[0.03] border border-white/10 p-3">
      <div className="text-sm text-white/60">{title}</div>
      <div className="font-semibold">{value}</div>
    </div>
  );
}

/** ======================= UTILS ======================= */
function downloadJSON(draft: Draft) {
  const data = { ...draft, _exportedAt: new Date().toISOString() };
  const blob = new Blob([JSON.stringify(data, null, 2)], {
    type: 'application/json',
  });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = `${(draft.identidade.nome || 'ficha').replace(
    /[^a-z0-9_-]/gi,
    '_'
  )}.json`;
  a.click();
  URL.revokeObjectURL(a.href);
}
