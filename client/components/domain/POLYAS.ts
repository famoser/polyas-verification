export type ElectionDetails = {
  title: Translation
}

export type Ballot = {
  id: string
  type: string
  contentAbove: Content
  title: Translation
  lists: List[]
  contentBelow: Content

  // properties left out which influence validation

  showInvalidOption: boolean
  showAbstainOption: boolean
}

export type List = {
  id: string
  candidates: Candidate[]

  // properties left out which influence validation

  voteCandidateXorList: boolean
}

export type Candidate = {
  id: string
  columns: Content[]
}

export type Content = {
  contentType: string
  value: Translation
}

export type Translation = {
  default: string
}
