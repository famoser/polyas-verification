
# Example data for the second device verification

This document contains sample data extracted from an actual protocol run
between the second device and the election system backend. It also includes
some intermediate computational steps, potentially useful for debugging.

For the protocol definition, see Appendinx B of
[polyas3.0-verifiable-v2.0.pdf](https://github.com/polyas-voting/core3-verifiable-doc/blob/main/pdf/polyas3.0-verifiable-v2.0.pdf).


### Content of the QR code

    encC  = J8kdvzMZHPQBHbVoNx+JrstXlgbhOqDIikjbOGOL9HvWVdRPWuw/Xbvq/nKS/mqH/h2FJjCYbozkJiq7
    encD  = /EhRgDjIA+scXsSyfSXvqPCHsvbf/UozQicLbNd4bjkps8aP4ZXdo3R+KuvYX/ZM8NeAJcGrZbeb3wm8fgnby1gQJGqJwMY+eN6qXN83b0i5pNaej1WrMglE4KIXpDc8Bn00stxsvy0qlw==
    vid   = voter7
    nonce = e552502592f5bec54e4750c769ae9a3ec913c69a7cd828ce0226201476a2f833

### Election status response (not relevant)
    
    {
      "title": {
        "default": "My Election Title",
        "value": {}
      },
      "languages": [
        "EN",
        "DE"
      ]
    }

### Login request

    {
      "voterId": "voter7",
      "ballotReference": "/EhRgDjIA+scXsSyfSXvqPCHsvbf/UozQicLbNd4bjkps8aP4ZXdo3R+KuvYX/ZM8NeAJcGrZbeb3wm8fgnby1gQJGqJwMY+eN6qXN83b0i5pNaej1WrMglE4KIXpDc8Bn00stxsvy0qlw==",
      "nonce": "e552502592f5bec54e4750c769ae9a3ec913c69a7cd828ce0226201476a2f833",
      "password": "711852",
      "challengeCommitment": "02438c535a05445cac1b15e017cc0755babaedd418d6bd581d51b87aae93a9fd14"
    }

See [below](#challenge-request) for the used challenge and challenge random coin.

### Login response

    {
      "value": {
        "token": "dm90ZXI3.aXhCaVJFRWtlWVpzRUVpNw==",
        "ballotVoterId": "voter7",
        "electionId": "0d6533e3-4d3f-4756-83c7-b03765460f62",
        "languages": [
          "EN",
          "DE"
        ],
        "title": {
          "default": "My Election Title",
          "value": {}
        },
        "contentAbove": {
          "value": {
            "default": "This is content above",
            "value": {}
          },
          "contentType": "TEXT"
        },
        "publicLabel": "A",
        "messages": {},
        "allowInvalid": true,
        "initialMessage": "{\"secondDeviceParametersJson\":\"{\\\"publicKey\\\":\\\"0390c059a207899b2dd76d5f5b7f40c73a02e620b67a5a23cc07134c3462b659aa\\\",\\\"verificationKey\\\":\\\"308201a2300d06092a864886f70d01010105000382018f003082018a0282018100db68673690d266f8ce7c0b718ca3be22f74a0ffe28ba1205bc68fe31677e422fa98b602870fa2d699df9c4f3a983a6fc08b93bae559b8b8e2c16483b24bc789066831ff4e063998590fca2e8f431f2a1716c1da6771377c1255e68d8334a160f8fe4c8a490d58675b24df04bfe6226d97e3a3af97cff761daf2d4ef8e7a9262335b4ab64222b841fb32a043bc454b65099092f432dcb3d2b3b76827555c18f7f7163cb4b7bae015d2e0007de08f6c00bfbf3f1087d291e3d4d5f7bf4b267213ea9f3e531aade52bb7084ab83f638075baa36133ad9eaf85e974c5ffe2709cc6286ce92a205c6b8f111169e7e71937741ae1983518388505943ff7ff858363fa6c6403c9e1d82e9b16fb69368895ebd800f68b46f9060ec533ccbf474d55b98d9d1f71fc8f7cf7149a0fba9e06226536394ea5902c7b1105c3cc22ce031edaace55130a0815fa293f5a55ea9d4f6c3f4db3ad0f843d63a8ea87db946f1ae26a81242424c03c0b71393948b436351ab5c7e6cf40c52816709afe1521f5c070b6770203010001\\\",\\\"ballots\\\":[{\\\"type\\\":\\\"STANDARD_BALLOT\\\",\\\"id\\\":\\\"A\\\",\\\"title\\\":{\\\"default\\\":\\\"Ballot title\\\",\\\"value\\\":{}},\\\"lists\\\":[{\\\"id\\\":\\\"A1\\\",\\\"title\\\":{\\\"default\\\":\\\"First question!\\\",\\\"value\\\":{}},\\\"columnHeaders\\\":[{\\\"default\\\":\\\"\\\",\\\"value\\\":{}}],\\\"candidates\\\":[{\\\"id\\\":\\\"A1-1\\\",\\\"columns\\\":[{\\\"value\\\":{\\\"default\\\":\\\"Yes\\\",\\\"value\\\":{}},\\\"contentType\\\":\\\"TEXT\\\"}],\\\"maxVotes\\\":1,\\\"minVotes\\\":0},{\\\"id\\\":\\\"A1-2\\\",\\\"columns\\\":[{\\\"value\\\":{\\\"default\\\":\\\"No\\\",\\\"value\\\":{}},\\\"contentType\\\":\\\"TEXT\\\"}],\\\"maxVotes\\\":1,\\\"minVotes\\\":0}],\\\"maxVotesOnList\\\":1,\\\"minVotesOnList\\\":1,\\\"maxVotesForList\\\":0,\\\"minVotesForList\\\":0,\\\"voteCandidateXorList\\\":false}],\\\"showInvalidOption\\\":true,\\\"showAbstainOption\\\":false,\\\"maxVotes\\\":1,\\\"minVotes\\\":0,\\\"prohibitMoreVotes\\\":false,\\\"prohibitLessVotes\\\":false,\\\"calculateAvailableVotes\\\":false,\\\"voterClientSettings\\\":{\\\"calculateAvailableVotes\\\":false,\\\"hideVoteCountForLists\\\":false,\\\"showInvalidOption\\\":true,\\\"showAbstainOption\\\":false,\\\"prohibitMoreVotes\\\":false,\\\"prohibitLessVotes\\\":false}}]}\",\"comSeed\":\"6b08702a1930646f9fbb80c9f57ba75a02110939195d6b5f7790062ac86b5e82\",\"ballot\":{\"encryptedChoice\":{\"ciphertexts\":[{\"x\":\"0223b5b92fe26dc525e2aef344ec247f4c2cd35eac2a9748768e9487dd6056ab03\",\"y\":\"0368514b71bc2e243850328652ce99e54ae0176daec225489a35011037a1c35362\"}]},\"zkp\":[{\"c\":\"9479545227975304595134386430197534597537279993655528155638900822488084376608\",\"f\":\"5521056301668359814466015156148686424524640301259987369517752364332332740211\"}],\"publicLabel\":\"A\",\"reference\":\"aENXTx-4eutmE-L32MeZ-ehag\",\"signature\":{\"c\":\"95061776160503324099948676648615761587988737747086421170121227405531782484838\",\"f\":\"61080424863856627347423798737103734315176236995280590044528897283323801137066\"}},\"signatureHex\":\"44f547c20f60ce2a67dbd19c342530f836d4e1727573c38ca4844db73ae88b8495692cbd1d56db0724c56a9818f6a9e320fd473d560c892ffcaac9697ada6c3875578f6b00db89294088435e0914e4ab9508c173fe763fa5ca8726a8642d343fc801186861bca29271be2e88e149ed655aa7b12535f7728645f47fdd6e0244981e7699d94da466f6554314ac0fec5a83508143e1fb4d96efc69c5c7cc498a5917031f021a1011b8e923316116fc4ed98b3d097c4f23dfbf833ce6357c9b494b84cf11470cc885ccb089cedc841128174939e1dfd11a8468aba359aa5864e97086b0b31464913216b639d64a59d54113906dc9edae006648196c321d8b1eb0944f00dc717fa6abd71bf78f051b6893f7a9cf3bd5039d9a4e6bfa9bf9a2a10f6efd9d12c80410616ceeb8fe3ab0307e53d500e56e31da0d5c94fbcce7f663f9e60f48b0265a7d008718b7e3ad94b00d13cbd3c77bd66649f7e354227437124cd8db86c2c5e5a64ea5c9c7e9bb48c95944a1e68ad281ea2d1b034f14f03eec120d2\",\"factorX\":[\"02c6b7cead1a06f0563352de0592a80e9c22210551c07ce3b38837c789393c80d6\"],\"factorY\":[\"03abf72793cad412da56b977b128de9a3097e52a3ceecfa0aadc2d233727c9b105\"],\"factorA\":[\"035fda9e504e0c1b8275d9d920e729a52a86bbb6a5fc1c679ac51cbdbc56235780\"],\"factorB\":[\"0346f00259f21c9288510a173d546247d849138c30f17bcde4322165bf5be34c8a\"]}"
      },
      "status": "OK"
    }

where the field initialMessage contains a JSON string with the following object:

    {
      "secondDeviceParametersJson": "{\"publicKey\":\"0390c059a207899b2dd76d5f5b7f40c73a02e620b67a5a23cc07134c3462b659aa\",\"verificationKey\":\"308201a2300d06092a864886f70d01010105000382018f003082018a0282018100db68673690d266f8ce7c0b718ca3be22f74a0ffe28ba1205bc68fe31677e422fa98b602870fa2d699df9c4f3a983a6fc08b93bae559b8b8e2c16483b24bc789066831ff4e063998590fca2e8f431f2a1716c1da6771377c1255e68d8334a160f8fe4c8a490d58675b24df04bfe6226d97e3a3af97cff761daf2d4ef8e7a9262335b4ab64222b841fb32a043bc454b65099092f432dcb3d2b3b76827555c18f7f7163cb4b7bae015d2e0007de08f6c00bfbf3f1087d291e3d4d5f7bf4b267213ea9f3e531aade52bb7084ab83f638075baa36133ad9eaf85e974c5ffe2709cc6286ce92a205c6b8f111169e7e71937741ae1983518388505943ff7ff858363fa6c6403c9e1d82e9b16fb69368895ebd800f68b46f9060ec533ccbf474d55b98d9d1f71fc8f7cf7149a0fba9e06226536394ea5902c7b1105c3cc22ce031edaace55130a0815fa293f5a55ea9d4f6c3f4db3ad0f843d63a8ea87db946f1ae26a81242424c03c0b71393948b436351ab5c7e6cf40c52816709afe1521f5c070b6770203010001\",\"ballots\":[{\"type\":\"STANDARD_BALLOT\",\"id\":\"A\",\"title\":{\"default\":\"Ballot title\",\"value\":{}},\"lists\":[{\"id\":\"A1\",\"title\":{\"default\":\"First question!\",\"value\":{}},\"columnHeaders\":[{\"default\":\"\",\"value\":{}}],\"candidates\":[{\"id\":\"A1-1\",\"columns\":[{\"value\":{\"default\":\"Yes\",\"value\":{}},\"contentType\":\"TEXT\"}],\"maxVotes\":1,\"minVotes\":0},{\"id\":\"A1-2\",\"columns\":[{\"value\":{\"default\":\"No\",\"value\":{}},\"contentType\":\"TEXT\"}],\"maxVotes\":1,\"minVotes\":0}],\"maxVotesOnList\":1,\"minVotesOnList\":1,\"maxVotesForList\":0,\"minVotesForList\":0,\"voteCandidateXorList\":false}],\"showInvalidOption\":true,\"showAbstainOption\":false,\"maxVotes\":1,\"minVotes\":0,\"prohibitMoreVotes\":false,\"prohibitLessVotes\":false,\"calculateAvailableVotes\":false,\"voterClientSettings\":{\"calculateAvailableVotes\":false,\"hideVoteCountForLists\":false,\"showInvalidOption\":true,\"showAbstainOption\":false,\"prohibitMoreVotes\":false,\"prohibitLessVotes\":false}}]}",
      "comSeed": "6b08702a1930646f9fbb80c9f57ba75a02110939195d6b5f7790062ac86b5e82",
      "ballot": {
        "encryptedChoice": {
          "ciphertexts": [
            {
              "x": "0223b5b92fe26dc525e2aef344ec247f4c2cd35eac2a9748768e9487dd6056ab03",
              "y": "0368514b71bc2e243850328652ce99e54ae0176daec225489a35011037a1c35362"
            }
          ]
        },
        "zkp": [
          {
            "c": "9479545227975304595134386430197534597537279993655528155638900822488084376608",
            "f": "5521056301668359814466015156148686424524640301259987369517752364332332740211"
          }
        ],
        "publicLabel": "A",
        "reference": "aENXTx-4eutmE-L32MeZ-ehag",
        "signature": {
          "c": "95061776160503324099948676648615761587988737747086421170121227405531782484838",
          "f": "61080424863856627347423798737103734315176236995280590044528897283323801137066"
        }
      },
      "signatureHex": "44f547c20f60ce2a67dbd19c342530f836d4e1727573c38ca4844db73ae88b8495692cbd1d56db0724c56a9818f6a9e320fd473d560c892ffcaac9697ada6c3875578f6b00db89294088435e0914e4ab9508c173fe763fa5ca8726a8642d343fc801186861bca29271be2e88e149ed655aa7b12535f7728645f47fdd6e0244981e7699d94da466f6554314ac0fec5a83508143e1fb4d96efc69c5c7cc498a5917031f021a1011b8e923316116fc4ed98b3d097c4f23dfbf833ce6357c9b494b84cf11470cc885ccb089cedc841128174939e1dfd11a8468aba359aa5864e97086b0b31464913216b639d64a59d54113906dc9edae006648196c321d8b1eb0944f00dc717fa6abd71bf78f051b6893f7a9cf3bd5039d9a4e6bfa9bf9a2a10f6efd9d12c80410616ceeb8fe3ab0307e53d500e56e31da0d5c94fbcce7f663f9e60f48b0265a7d008718b7e3ad94b00d13cbd3c77bd66649f7e354227437124cd8db86c2c5e5a64ea5c9c7e9bb48c95944a1e68ad281ea2d1b034f14f03eec120d2",
      "factorX": [
        "02c6b7cead1a06f0563352de0592a80e9c22210551c07ce3b38837c789393c80d6"
      ],
      "factorY": [
        "03abf72793cad412da56b977b128de9a3097e52a3ceecfa0aadc2d233727c9b105"
      ],
      "factorA": [
        "035fda9e504e0c1b8275d9d920e729a52a86bbb6a5fc1c679ac51cbdbc56235780"
      ],
      "factorB": [
        "0346f00259f21c9288510a173d546247d849138c30f17bcde4322165bf5be34c8a"
      ]
    }

where the field `secondDevicePublicParametersJson` contains a JSON string with the following object: 

    {
      "publicKey": "0390c059a207899b2dd76d5f5b7f40c73a02e620b67a5a23cc07134c3462b659aa",
      "verificationKey": "308201a2300d06092a864886f70d01010105000382018f003082018a0282018100db68673690d266f8ce7c0b718ca3be22f74a0ffe28ba1205bc68fe31677e422fa98b602870fa2d699df9c4f3a983a6fc08b93bae559b8b8e2c16483b24bc789066831ff4e063998590fca2e8f431f2a1716c1da6771377c1255e68d8334a160f8fe4c8a490d58675b24df04bfe6226d97e3a3af97cff761daf2d4ef8e7a9262335b4ab64222b841fb32a043bc454b65099092f432dcb3d2b3b76827555c18f7f7163cb4b7bae015d2e0007de08f6c00bfbf3f1087d291e3d4d5f7bf4b267213ea9f3e531aade52bb7084ab83f638075baa36133ad9eaf85e974c5ffe2709cc6286ce92a205c6b8f111169e7e71937741ae1983518388505943ff7ff858363fa6c6403c9e1d82e9b16fb69368895ebd800f68b46f9060ec533ccbf474d55b98d9d1f71fc8f7cf7149a0fba9e06226536394ea5902c7b1105c3cc22ce031edaace55130a0815fa293f5a55ea9d4f6c3f4db3ad0f843d63a8ea87db946f1ae26a81242424c03c0b71393948b436351ab5c7e6cf40c52816709afe1521f5c070b6770203010001",
      "ballots": [
        {
          "type": "STANDARD_BALLOT",
          "id": "A",
          "title": {
            "default": "Ballot title",
            "value": {}
          },
          "lists": [
            {
              "id": "A1",
              "title": {
                "default": "First question!",
                "value": {}
              },
              "columnHeaders": [
                {
                  "default": "",
                  "value": {}
                }
              ],
              "candidates": [
                {
                  "id": "A1-1",
                  "columns": [
                    {
                      "value": {
                        "default": "Yes",
                        "value": {}
                      },
                      "contentType": "TEXT"
                    }
                  ],
                  "maxVotes": 1,
                  "minVotes": 0
                },
                {
                  "id": "A1-2",
                  "columns": [
                    {
                      "value": {
                        "default": "No",
                        "value": {}
                      },
                      "contentType": "TEXT"
                    }
                  ],
                  "maxVotes": 1,
                  "minVotes": 0
                }
              ],
              "maxVotesOnList": 1,
              "minVotesOnList": 1,
              "maxVotesForList": 0,
              "minVotesForList": 0,
              "voteCandidateXorList": false
            }
          ],
          "showInvalidOption": true,
          "showAbstainOption": false,
          "maxVotes": 1,
          "minVotes": 0,
          "prohibitMoreVotes": false,
          "prohibitLessVotes": false,
          "calculateAvailableVotes": false,
          "voterClientSettings": {
            "calculateAvailableVotes": false,
            "hideVoteCountForLists": false,
            "showInvalidOption": true,
            "showAbstainOption": fa`else,
            "prohibitMoreVotes": false,
            "prohibitLessVotes": false
          }
        }
      ]
    }


### Decrypting the payloads encC and encD

The encrypted payloads `encC` and `encD` from the QR-code (see [Content of the
QR code](#content-of-the-qr-code)) can be decrypted, as described in B.4, using
the `comSeed` from the login response

    comSeed = 6b08702a1930646f9fbb80c9f57ba75a02110939195d6b5f7790062ac86b5e82

to retrieve `randomCoinSeed` and the *linking label* (reference coin). 

For our example data, this gives:

    referenceCoin = 111157447817800309944456085702871666051502142121739833289808289305713767858164
    randomCoinSeed = 4f1d5585ad1130471ed4960a19d9bf20d60b76be8661b343fa68426e7402a2cf


### Computing the reference

To check the correctness of the association of the ballot to the voter
identity (the second condition of B.6), one needs to re-compute the reference
(*ref*) from the voter identity and the provided linking label
(`referenceCoin`).

This reference is computed as a Pedersen commitment on the uniform hash of
voter identifier, with linking label (reference coin) serving as the random
coin, where the final value is formatted (and truncated) using SHA-246,
transforming it to Base-64, taking 22 characters and grouping by 6 characters
separated by '-', as illustrated by the following pseudo-code:

    commitment = pedersenCommitment(uniformHash(voterId), referenceCoin)
    hash = sha256(group.asBytes(commitment))
    reference = toBase64String(hash).take(22).chunked(6).joinToString("-")

For our example data with

    voterId = "voter7"
    referenceCoin = 111157447817800309944456085702871666051502142121739833289808289305713767858164

the result is 

    reference = "aENXTx-4eutmE-L32MeZ-ehag"

with the following intermediate values:

    uniformHash(voterId) = 75257976807143615402452449431801958905811497121857943123079219007777383312406
    commitment = 0302c9cbe5ed0cb098e2c99b13c8079563c714c6cfb5b9a8e0c32c2e2e8b60dbc1
    hash = 6843574f1e1ebad9842f7d8c7997a16a0e9d82fc39efa362e14684ecd5054226


### Checking the signature


In order to check the signature, one needs to transform the ballot to 
its normalized representation (see Algorithm 14). This should yield

    00000001000000210223b5b92fe26dc525e2aef344ec247f4c2cd35eac2a9748768e9487dd6056ab03000000210368514b71bc2e243850328652ce99e54ae0176daec225489a35011037a1c3536200000001410000001961454e5854782d346575746d452d4c33324d655a2d65686167000000010000002014f53b82a73bc800e42e4008abdc1247871a33f02a26f4c05883d85a960bec20000000200c34ce9feca55d8e61e443dcb397f4863f7db2edab4124088350de5e66f1ee730000002100d22b0f004c5029024f90919745956ea5f18a1a380a6d7289218b6f42bf5e93660000002100870a4b9bea8ab5d89a67fd9c5f3f9b1a6404d967bb986129ac618e6ee1cc6faa

The signature is on the SHA-256 hash of the above value, represented as hexadecimal string:

    hashBallotHex = 1160ba5fc7878405b96516ed371e547b5f107ccdeea838aa577d4aea75c36ef2

One should verify that the signature from the login response

    signatureHex: 44f547c20f60ce2a67dbd19c342530f836d4e1727573c38ca4844db73ae88b8495692cbd1d56db0724c56a9818f6a9e320fd473d560c892ffcaac9697ada6c3875578f6b00db89294088435e0914e4ab9508c173fe763fa5ca8726a8642d343fc801186861bca29271be2e88e149ed655aa7b12535f7728645f47fdd6e0244981e7699d94da466f6554314ac0fec5a83508143e1fb4d96efc69c5c7cc498a5917031f021a1011b8e923316116fc4ed98b3d097c4f23dfbf833ce6357c9b494b84cf11470cc885ccb089cedc841128174939e1dfd11a8468aba359aa5864e97086b0b31464913216b639d64a59d54113906dc9edae006648196c321d8b1eb0944f00dc717fa6abd71bf78f051b6893f7a9cf3bd5039d9a4e6bfa9bf9a2a10f6efd9d12c80410616ceeb8fe3ab0307e53d500e56e31da0d5c94fbcce7f663f9e60f48b0265a7d008718b7e3ad94b00d13cbd3c77bd66649f7e354227437124cd8db86c2c5e5a64ea5c9c7e9bb48c95944a1e68ad281ea2d1b034f14f03eec120d2

is a valid signature on `hashBallotHex` under the verification key from the
second-device parameters. In our running example the verification key is

    verificationKey = 308201a2300d06092a864886f70d01010105000382018f003082018a0282018100db68673690d266f8ce7c0b718ca3be22f74a0ffe28ba1205bc68fe31677e422fa98b602870fa2d699df9c4f3a983a6fc08b93bae559b8b8e2c16483b24bc789066831ff4e063998590fca2e8f431f2a1716c1da6771377c1255e68d8334a160f8fe4c8a490d58675b24df04bfe6226d97e3a3af97cff761daf2d4ef8e7a9262335b4ab64222b841fb32a043bc454b65099092f432dcb3d2b3b76827555c18f7f7163cb4b7bae015d2e0007de08f6c00bfbf3f1087d291e3d4d5f7bf4b267213ea9f3e531aade52bb7084ab83f638075baa36133ad9eaf85e974c5ffe2709cc6286ce92a205c6b8f111169e7e71937741ae1983518388505943ff7ff858363fa6c6403c9e1d82e9b16fb69368895ebd800f68b46f9060ec533ccbf474d55b98d9d1f71fc8f7cf7149a0fba9e06226536394ea5902c7b1105c3cc22ce031edaace55130a0815fa293f5a55ea9d4f6c3f4db3ad0f843d63a8ea87db946f1ae26a81242424c03c0b71393948b436351ab5c7e6cf40c52816709afe1521f5c070b6770203010001


### Challenge request
    
    {
      "challenge": "43030445049487747541029726235731232721292906839186252755320709847982812472754",
      "challengeRandomCoin": "26575495741935064455070503300995101427899481700480831329969424882911236540902"
    }

### Challenge response
 
    {
      "z": [
        "4401406914288698047757179140920661965801548002395469080992993955987699436805817688780986332798936715875035989600840094651217115975777251626648000405127539"
      ]
    }


### ZKP validation and extraction of the voter's choice

The ZKP validation should follow Algorithm 15.
For the running example, the random coins derived from the seed are:

    [ 40237237455298050319120936869549056473558681858388723323151943196272052465320 ]


The extracted (plaintext) voter's choice is:

    decryptedChoice = 00000100

