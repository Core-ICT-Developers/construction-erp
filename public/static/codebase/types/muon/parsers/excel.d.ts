import { IFormula } from "../types";
import { parseContext } from "../types";
export declare function position(raw: number[], word: string, page: number): void;
export declare function parse(text: string, ctx: parseContext): IFormula;
