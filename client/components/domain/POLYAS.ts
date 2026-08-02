export type ElectionDetails = {
  title: Translation
}

export type Ballot = {
  id: string
  type: string
  contentAbove?: any
  title: Translation
  lists: List[]
  contentBelow?: any

  // properties left out which influence validation

  showInvalidOption: boolean
  showAbstainOption: boolean
}

export type List = {
  id: string
  candidates: Candidate[]
  columnHeaders: Translation[]

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
