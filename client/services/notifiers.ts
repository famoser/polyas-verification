import Noty from 'nano-noty'

Noty.overrideDefaults({
  theme: 'bootstrap-v5',
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
