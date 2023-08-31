export class DynamicTableDTO {
    constructor(
        public readonly columnDescription: string,
        public readonly columnName: string,
        public readonly columnRules: any,
        public readonly allowEdit = true,
    ) {}
}

export type DynamicTableDTOList = DynamicTableDTO[];
