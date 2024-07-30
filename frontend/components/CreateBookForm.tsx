import { zodResolver } from '@hookform/resolvers/zod';
import { Alert, Button, Container, Form } from 'react-bootstrap';
import { SubmitHandler, useForm } from 'react-hook-form';
import classes from '@/styles/components/CreateBookForm.module.css';
import { createBookFormSchema, CreateBookFormType } from '@/validation/create-book-form.validation';
import { BookCreationDto } from '@/types/book-types';
import { addMinutes, format } from 'date-fns';
import { useCreateBook } from '@/service/book-service';

export default function CreateBookForm() {
    const {
        register,
        handleSubmit,
        reset,
        formState: { errors },
    } = useForm<CreateBookFormType>({ resolver: zodResolver(createBookFormSchema) });

    const { sendData, isLoading, error } = useCreateBook();

    const onSubmit: SubmitHandler<CreateBookFormType> = async (values) => {
        const creationDate = new Date(values.creation_date);
        const bookCreationDto: BookCreationDto = {
            title: values.title,
            author: values.author,
            createdDate: format(addMinutes(creationDate, creationDate.getTimezoneOffset()), 'yyyy-MM-dd HH:mm:ss'),
            isbn: values.isbn,
            description: values.description,
        };
        sendData(bookCreationDto);

        reset();
    };

    return (
        <Container className="items-center justify-center lg:px-10 lg:py-20">
            {error && <Alert variant="danger">{error.message}</Alert>}
            <Form name="create-book" className="space-y-8" onSubmit={handleSubmit(onSubmit)}>
                <Container className="grid gap-4 md:grid-cols-2">
                    <Container>
                        <Form.Group controlId="book-title">
                            <Form.Label>Book Title</Form.Label>
                            <Form.Control className={`${classes.inputField} bg-transparent py-3 px-6 text-white placeholder-neutral-400 border-neutral-600 rounded-lg w-full`} type="text" placeholder="Title of the book" {...register('title')} />
                            {errors.title && <Form.Text className="text-danger">{errors.title.message}</Form.Text>}
                        </Form.Group>
                    </Container>

                    <Container>
                        <Form.Group controlId="book-author">
                            <Form.Label>Book Author</Form.Label>
                            <Form.Control className={`${classes.inputField} bg-transparent py-3 px-6 text-white placeholder-neutral-400 border-neutral-600 rounded-lg w-full`} type="text" placeholder="Author of the book" {...register('author')} />
                            {errors.author && <Form.Text className="text-danger">{errors.author.message}</Form.Text>}
                        </Form.Group>
                    </Container>
                </Container>

                <Container className="grid gap-4 md:grid-cols-2">
                    <Container>
                        <Form.Group controlId="book-date">
                            <Form.Label>Book Creation Date</Form.Label>
                            <input type="date" className={`${classes.datePicker} bg-transparent py-3 px-6 text-white placeholder-neutral-400 border-neutral-600 rounded-lg w-full`} {...register('creation_date')} />
                            {errors.creation_date && (
                                <Form.Text className="text-danger">{errors.creation_date.message}</Form.Text>
                            )}
                        </Form.Group>
                    </Container>

                    <Container>
                        <Form.Group controlId="book-isbn">
                            <Form.Label>Book ISBN</Form.Label>
                            <Form.Control className={`${classes.inputField} bg-transparent py-3 px-6 text-white placeholder-neutral-400 border-neutral-600 rounded-lg w-full`} type="text" placeholder="Primary ISBN" {...register('isbn')} />
                            {errors.isbn && <Form.Text className="text-danger">{errors.isbn.message}</Form.Text>}
                        </Form.Group>
                    </Container>
                </Container>

                <Container>
                    <Form.Group controlId="book-description">
                        <Form.Label>Book Description</Form.Label>
                        <Form.Control className={`${classes.inputField} bg-transparent py-3 px-6 text-white placeholder-neutral-400 border-neutral-600 rounded-lg w-full`}
                            as={'textarea'}
                            rows={6}
                            placeholder="Give a short description"
                            {...register('description')}
                        />
                        {errors.description && (
                            <Form.Text className="text-danger">{errors.description.message}</Form.Text>
                        )}
                    </Form.Group>
                </Container>

                <Button
                    type="submit"
                    className={`${classes.submitButton} w-full rounded-lg bg-white px-6 py-3 font-semibold text-neutral-900 outline-none hover:animate-pulse`}
                >
                    {isLoading ? <p>Sending...</p> : <p>Create new book</p>}
                </Button>
            </Form>
        </Container>
    );
}
