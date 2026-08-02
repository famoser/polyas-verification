import { displayError } from './notifiers'
import type { Receipt, Status } from '@/components/domain/Status'
import type { Election } from '@/components/domain/Election'
import type { Verification } from '@/components/domain/Verification'
import type { Ballot, ElectionDetails } from '@/components/domain/POLYAS'

const httpClient = {
  request: async function (url: string, init: RequestInit = {}): Promise<Response | null> {
    let response: Response

    if (window.location.hostname === 'localhost') {
      url = 'https://localhost:8000' + url
    }

    try {
      response = await fetch(url, init)
    } catch (error) {
      const err = error as Error
      if (err.name === 'AbortError') {
        // hide aborted errors (happens when navigating rapidly in firefox)
        return null
      }

      console.log(error)
      displayError('Failed: ' + String(error))

      throw error
    }

    if (!response.ok) {
      const errorText = response.status + ': ' + response.statusText
      const error = new Error(errorText)

      console.log(error)
      displayError('Failed with error ' + errorText)

      throw error
    }

    return response
  }
}

const restClient = {
  get: async function (url: string, options: RequestInit = {}) {
    const response = await httpClient.request(url, options)
    return response?.json()
  },
  post: async function (url: string, post: object, options: RequestInit = {}) {
    const init: RequestInit = {
      ...options,
      body: JSON.stringify(post),
      method: 'POST'
    }

    const response = await httpClient.request(url, init)
    return response?.json()
  },
  postDownload: async function (url: string, post: object, options: RequestInit = {}) {
    const init: RequestInit = {
      ...options,
      body: JSON.stringify(post),
      method: 'POST'
    }

    const response = await httpClient.request(url, init)
    return response?.bytes()
  }
}

const api = {
  getElection: async function () {
    return (await restClient.get('/api/election')) as Election
  },
  getElectionDetails: async function () {
    return (await restClient.get('/api/electionDetails')) as ElectionDetails
  },
  getBallots: async function () {
    return (await restClient.get('/api/ballots')) as Ballot[]
  },
  postVerification: async function (data: Verification) {
    return (await restClient.post('/api/verification', data)) as Status
  },
  postReceipt: async function (receipt: File) {
    const data = new FormData()
    data.append('receipt', receipt)
    return (await restClient.post('/api/receipt', data)) as Status
  },
  postDownloadReceipt: async function (receipt: Receipt) {
    return await restClient.postDownload('/api/receipt/download', receipt)
  }
}

export { api }
