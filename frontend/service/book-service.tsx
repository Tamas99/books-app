
export async function getBooks() {
    const response = await fetch(
        'http://localhost:8000/api/v1/books?sort_by=publication_time&sort_order=desc&page=1&page_size=10',
    );

    if (!response.ok) {
        console.log(response);
        throw new Error('Something went wrong.');
    }

    return response.json();
}
