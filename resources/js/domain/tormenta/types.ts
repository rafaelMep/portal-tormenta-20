export type Attr = 'FOR' | 'DES' | 'CON' | 'INT' | 'SAB' | 'CAR';

export interface Skill {
  id: number;
  slug: string;
  name: string;
  key_attr: Attr;
  trained_only?: boolean;
  armor_penalty?: boolean;
  meta?: any;
}

export interface RaceChoiceSetOption {
  id: number;
  set_id: number;
  value: Attr;
  label?: string | null;
  meta?: any;
}

export interface RaceChoiceSet {
  id: number;
  race_id: number;
  key: string;
  label?: string | null;
  min_picks: number;
  max_picks: number;
  constraints?: { exclude?: Attr[] } | any;
  meta?: any;
  options: RaceChoiceSetOption[];
}

export interface RaceChoiceGroupOption {
  id: number;
  group_id: number;
  key: string;
  name: string;
  summary?: string | null;
  meta?: any;
}

export interface RaceChoiceGroup {
  id: number;
  race_id: number;
  key: string;
  name: string;
  min_choices: number;
  max_choices: number;
  required: boolean;
  sort: number;
  meta?: any;
  options: RaceChoiceGroupOption[];
}

export interface AttributeModRow {
  id: number;
  race_id?: number;
  race_variant_id?: number;

  choice_option_id?: number | null;
  mode: 'fixed' | 'choice';

  attribute: Attr | null;
  modifier: number;
  quantity?: number;

  exclusions?: Attr[] | null;
  notes?: string | null;
}

export interface RaceVariant {
  id: number;
  race_id: number;
  key: string;
  name: string;
  summary?: string | null;
  meta?: any;

  attribute_mods?: AttributeModRow[];
}

export interface Race {
  id: number;
  slug: string;
  name: string;
  size: string;
  speed: number | null;
  creature_type?: string | null;
  summary?: string | null;
  meta?: any;

  variants?: RaceVariant[];
  attribute_mods?: AttributeModRow[];

  choice_sets?: RaceChoiceSet[];
  choice_groups?: RaceChoiceGroup[];
}

export interface Draft {
  identidade: { nome: string; nivel: number };

  raca: {
    id: number | null;
    slug: string | null;
    name: string | null;

    variant_id: number | null;
    variant_key: string | null;

    set_picks: { set_id: number; key: string; picks: Attr[] }[];
    group_picks: { group_id: number; option_ids: number[] }[];
  };

  atributos: {
    metodo: 'point-buy-10';
    pontos: number;
    base: Record<Attr, number>;
  };

  pericias: Record<
    string,
    { treinado: boolean; bonus: number; key_attr: Attr; name: string }
  >;
}
