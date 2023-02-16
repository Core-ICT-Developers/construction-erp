import { Grid } from "../../ts-grid";
import { DataPage } from "../../muon";
import { Spreadsheet } from "./Spreadsheet";
import { IBufferManager, ISelection } from "./types";
export interface ISelectedCell {
    row: string;
    col: string;
    cell: string;
    edit?: boolean;
}
export declare class Selection implements ISelection {
    private _spreadsheet;
    private _grid;
    private _bufferManager;
    private _events;
    private _mousePressed;
    private _resizedColumns;
    private _resizedRows;
    private _pressedArea;
    private _cellsToCopy;
    private _activePage;
    constructor(spreadsheet: Spreadsheet, grid: Grid, bufferManager: IBufferManager, activePage: DataPage);
    setSelectedCell(cell: string): void;
    getSelectedCell(): string;
    getFocusedCell(): string;
    setFocusedCell(cell: string): void;
    removeSelectedCell(cell?: string): void;
    private _isInRange;
    private _removeHeadersCss;
    private _setHeadersCss;
    private _selectRow;
    private _selectColumn;
    private _setGroupSelectionHandlers;
    private _setHandlers;
}
