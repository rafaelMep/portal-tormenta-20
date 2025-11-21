import type {
  Attr,
  Draft,
  Race,
  RaceVariant,
  RaceChoiceGroup,
  AttributeModRow,
} from './types';
import { ATTRS } from './attributes';

export function sumPointBuy(base: Record<Attr, number>) {
  let total = 0;
  for (const a of ATTRS) total += base[a] - 10;
  return total;
}

/** Normaliza mods de variante (pode vir de meta ou do padrão do back) */
export function normalizeVariantAttrMods(
  v?: RaceVariant | null
): { attribute: Attr; modifier: number }[] {
  if (!v) return [];

  if (v.attribute_mods?.length) {
    return v.attribute_mods
      .filter((m) => m.attribute)
      .map((m) => ({
        attribute: m.attribute as Attr,
        modifier: m.modifier,
      }));
  }

  const metaMods = v.meta?.attribute_mods as
    | { attribute: Attr; modifier: number }[]
    | undefined;

  return metaMods ?? [];
}

/** Gera lista completa de mods raciais aplicáveis */
export function gatherRacialMods(
  race: Race | null,
  variant: RaceVariant | null,
  setPicks: { set_id: number; key: string; picks: Attr[] }[],
  groupPicks: { group_id: number; option_ids: number[] }[],
  choiceGroups: RaceChoiceGroup[]
) {
  const mods: { attribute: Attr; modifier: number; source: string }[] = [];
  if (!race) return mods;

  const fullMods: AttributeModRow[] = race.attribute_mods ?? [];

  for (const m of fullMods) {
    if (m.mode === 'fixed' && !m.choice_option_id && m.attribute) {
      mods.push({
        attribute: m.attribute,
        modifier: m.modifier,
        source: `Raça: ${race.name}`,
      });
    }
  }

  if (groupPicks.length && fullMods.length) {
    const pickedOptionIds = new Set<number>();
    for (const g of groupPicks) {
      for (const id of g.option_ids) pickedOptionIds.add(id);
    }

    for (const m of fullMods) {
      if (m.choice_option_id && m.attribute && pickedOptionIds.has(m.choice_option_id)) {
        mods.push({
          attribute: m.attribute,
          modifier: m.modifier,
          source: 'Escolha de raça',
        });
      }
    }
  }

  for (const s of setPicks) {
    for (const pick of s.picks) {
      mods.push({
        attribute: pick,
        modifier: 1,
        source: `Escolha: ${s.key}`,
      });
    }
  }

  const varMods = normalizeVariantAttrMods(variant);
  for (const vm of varMods) {
    mods.push({
      attribute: vm.attribute,
      modifier: vm.modifier,
      source: `Variante: ${variant?.name ?? ''}`.trim(),
    });
  }

  return mods;
}

export function applyFinal(
  base: Record<Attr, number>,
  racial: { attribute: Attr; modifier: number }[]
) {
  const fin = { ...base } as Record<Attr, number>;
  for (const m of racial) {
    fin[m.attribute] = (fin[m.attribute] ?? 0) + m.modifier;
  }
  return fin;
}

export function initialDraft(skills: any[]): Draft {
  return {
    identidade: { nome: '', nivel: 1 },
    raca: {
      id: null,
      slug: null,
      name: null,
      variant_id: null,
      variant_key: null,
      set_picks: [],
      group_picks: [],
    },
    atributos: {
      metodo: 'point-buy-10',
      pontos: 10,
      base: { FOR: 10, DES: 10, CON: 10, INT: 10, SAB: 10, CAR: 10 },
    },
    pericias: Object.fromEntries(
      skills.map((s: any) => [
        s.slug,
        {
          treinado: false,
          bonus: 0,
          key_attr: s.key_attr as Attr,
          name: s.name,
        },
      ])
    ),
  };
}
