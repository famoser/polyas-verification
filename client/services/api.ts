import axios from 'axios'
import { displayError } from './notifiers'
import type { Receipt, Status } from '@/components/domain/Status'
import type { Election } from '@/components/domain/Election'
import type { Verification } from '@/components/domain/Verification'
import type { Ballot, ElectionDetails } from '@/components/domain/POLYAS'

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
  getElectionDetails: async function () {
    const response = await axios.get('/api/electionDetails')
    return response.data as ElectionDetails
  },
  getBallots: async function () {
    const response = await axios.get('/api/ballots')
    return response.data as Ballot[]
  },
  postVerification: async function (data: Verification) {
    const response = await axios.post('/api/verification', data)
    return JSON.parse(response.data) as Status
  },
  postReceipt: async function (receipt: File) {
    const data = new FormData()
    data.append('receipt', receipt)
    const response = await axios.post('/api/receipt', data)
    return JSON.parse(response.data) as Status
  },
  postStoreReceipt: async function (receipt: Receipt) {
    const response = await axios.post('/api/receipt/store', receipt)
    return JSON.parse(response.data) as Status
  },
  postDownloadReceipt: async function (receipt: Receipt) {
    const response = await axios.post('/api/receipt/download', receipt)
    return response.data
  }
}

export { api }
