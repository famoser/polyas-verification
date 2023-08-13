import Noty from 'noty'

Noty.overrideDefaults({
  theme: 'bootstrap-v4',
  type: 'success'
})

const displayError = function (errorMessage: string) {
  new Noty({
    text: errorMessage,
    type: 'error',
    timeout: false
  }).show()
}

export { displayError }
