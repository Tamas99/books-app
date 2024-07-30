import axios, { AxiosInstance } from "axios";
import { BOOK_API_URL } from "../config/constants";

export function getBookApi(): AxiosInstance {
    return axios.create({ baseURL: BOOK_API_URL })
}
