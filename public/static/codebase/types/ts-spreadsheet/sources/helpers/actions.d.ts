import { IGrid, IGridConfig } from "../../../ts-grid";
import { DataPage } from "../../../muon";
export declare function getHeaderCell(letter: string, index: number): string;
export declare function getIndexRowCell(index: number): string;
export declare function updateColumns(config: IGridConfig): void;
export declare function updateRowsIndex(data: any): void;
export declare function removeRowsCss(grid: IGrid): void;
export declare function setAutoHeightToRow(cell: string, grid: IGrid, page: DataPage, multiline: string): boolean;
