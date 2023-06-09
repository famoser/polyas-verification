import axios from 'axios'
import { displayError } from './notifiers'
import type { VerificationResult } from '@/components/domain/VerificationResult'
import type { Election } from '@/components/domain/Election'

let baseUrl = ''
if (window.location.hostname === 'localhost') {
  baseUrl += 'https://localhost:8000'
  axios.defaults.baseURL = baseUrl
}

const api = {
  addInterceptors: function (translator: (label: string) => string) {
    axios.interceptors.response.use(
      (response) => response,
      (error) => {
        console.log(error)

        let errorText = error
        if (error.response) {
          const response = error.response
          errorText = '(' + response.status + ' ' + response.statusText + ')'
          if (response.data && response.data.exception && response.data.exception[0].message) {
            errorText += ': ' + response.data.exception[0].message
          }
        }

        const errorMessage = translator('service.api.request_failed') + ' ' + errorText
        displayError(errorMessage)

        return Promise.reject(error)
      }
    )
  },
  getElection: async function () {
    const response = await axios.get('/api/election')
    return response.data as Election
  },
  postReceipt: async function (receipt: File) {
    const data = new FormData()
    data.append('receipt', receipt)
    const response = await axios.post('/api/receipt', data)
    return response.data as VerificationResult
  }
}

export { api }
