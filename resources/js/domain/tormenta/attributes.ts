import type { Attr } from './types';

export const ATTRS: Attr[] = ['FOR', 'DES', 'CON', 'INT', 'SAB', 'CAR'];

export const labelAttr = (a: Attr) =>
  ({
    FOR: 'Força',
    DES: 'Destreza',
    CON: 'Constituição',
    INT: 'Inteligência',
    SAB: 'Sabedoria',
    CAR: 'Carisma',
  }[a]);

export const mod = (v: number) => Math.floor((v - 10) / 2);

export const clamp = (v: number, min: number, max: number) =>
  Math.max(min, Math.min(max, v));
