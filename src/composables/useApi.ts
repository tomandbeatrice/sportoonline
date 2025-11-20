import axios from '@/utils/axios'

type ApiParams = Record<string, any>
type ApiResponse<T> = Promise<T>

export const useApi = () => {
  const get = async <T = any>(url: string, params?: ApiParams): ApiResponse<T> => {
    const { data } = await axios.get<T>(url, { params })
    return data
  }

  const post = async <T = any>(url: string, body?: any): ApiResponse<T> => {
    const { data } = await axios.post<T>(url, body)
    return data
  }

  const put = async <T = any>(url: string, body?: any): ApiResponse<T> => {
    const { data } = await axios.put<T>(url, body)
    return data
  }

  const del = async <T = any>(url: string): ApiResponse<T> => {
    const { data } = await axios.delete<T>(url)
    return data
  }

  return { get, post, put, del }
}