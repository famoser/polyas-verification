export type Status = {
  status: boolean
  error: string | null
  result: string | null
  receipt: Receipt | null
}

export type Receipt = {
  fingerprint: string
  signature: string
  ballotVoterId: string
}
