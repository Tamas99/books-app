export interface BookPageDto {
    items: BookDto[];
    current_page: number;
    total_page: number;
    total_results: number;
}

export interface BookDto {
    id: number;
    title: string;
    author: string;
    isbn: string;
}

export interface BookCreationDto {
    title: string;
    author: string;
    isbn: string;
    createdDate: string;
    description: string;
}
