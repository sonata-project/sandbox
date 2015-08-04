@api @get @media
Feature: Check the API for MediaBundle
  I want to test the GET API calls

  Background:
    Given I am authenticating as "admin" with "admin" password

  @api @media @list
  Scenario Outline: Retrieve the list of medias
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource              | status_code | format | message                        |
      | /api/media/media.json | 200         | json   | IMG_3008.jpg                   |
      | /api/media/media.xml  | 200         | xml    | switzerland_2012-05-19_006.jpg |

  @api @media @unique
  Scenario Outline: Retrieve a specific media information by unique id
    When I send a GET request to "<resource>"
    Then the response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/media/media/1.json | 200 | json | IMG_3587.jpg |
      | /api/media/media/1.xml  | 200 | xml  | IMG_3587.jpg |

  # cannot run this scenario on docker ci, files are not available
  #@api @media @unique @binary
  #Scenario Outline: Retrieve the media binaries for a unique ID
  #  When I send a GET request to "<resource>"
  #  Then the response code should be <status_code>
  #  And response should be a binary
  #
  #  Examples:
  #    | resource                                    | status_code |
  #    | /api/media/media/1/binaries/reference.json  | 200         |
  #    | /api/media/media/1/binaries/reference.xml   | 200         |
  #    | /api/media/media/1/binaries/reference.html  | 200         |

  @api @media @url @format @ok
  Scenario Outline: Return available urls for each media
    When I send a GET request to "<resource>"
    Then response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<class_format_2>"
    And response should contain "<class_format_3>"

    Examples:
      | resource| status_code | format | class_format_2 | class_format_3 |
      | /api/media/media/1/formats.json   | 200 | json  | thumb_1_default_small.jpeg | thumb_1_default_big.jpeg |
      | /api/media/media/1/formats.xml    | 200 | xml   | thumb_1_default_small.jpeg | thumb_1_default_big.jpeg |

  @api @media @url @format @ko
  Scenario Outline: Return media urls for each format
    When I send a GET request to "<resource>"
    Then response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/media/media/120/formats.json | 404 | json | Media (120) was not found |
      | /api/media/media/120/formats.xml  | 404 | xml  | Media (120) was not found |

  @api @gallery @list
  Scenario Outline: Retrive the list of available galleries
    When I send a GET request to "<resource>"
    Then response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message_1>"
    And response should contain "<message_2>"
    And response should contain "<message_3>"
    And response should contain "<message_4>"

    Examples:
      | resource| status_code | format | message_1 | message_2 | message_3 | message_4 |
      | /api/media/galleries.json | 200 | json | Japan | Switzerland | Canada | Paris |
      | /api/media/galleries.xml  | 200 | xml  | Japan | Switzerland | Canada | Paris |

  @api @gallery @unique @ok
  Scenario Outline: Retrieve a specific gallery by a unique ID
    When I send a GET request to "<resource>"
    Then response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
    | resource| status_code | format | message |
    | /api/media/galleries/1.json | 200 | json | default_format |
    | /api/media/galleries/1.xml  | 200 | xml  | default_format |

  @api @gallery @unique @ko
  Scenario Outline: Retrieve a specific gallery by a unique ID
    When I send a GET request to "<resource>"
    Then response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/media/galleries/120.json | 404 | json | Gallery (120) not found |
      | /api/media/galleries/120.xml  | 404 | xml  | Gallery (120) not found |

  @api @galleryhasmedias @gallery @unique @ok
  Scenario Outline: Retrieve the galleryhasmedias of a specific gallery by a unique ID
    When I send a GET request to "<resource>"
    Then response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message_1>"
    And response should contain "<message_2>"

    Examples:
    | resource| status_code | format | message_1 | message_2 |
    | /api/media/galleries/1/galleryhasmedias.json | 200 | json | gallery_id | media_id |
    | /api/media/galleries/1/galleryhasmedias.xml  | 200 | xml  | gallery_id | media_id |

  @api @galleryhasmedias @gallery @unique @ko
  Scenario Outline: Unable to retrieve the galleryhasmedias of a specific unexisting gallery by a unique ID
    When I send a GET request to "<resource>"
    Then response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/media/galleries/120/galleryhasmedias.json | 404 | json | Gallery (120) not found |
      | /api/media/galleries/120/galleryhasmedias.xml  | 404 | xml  | Gallery (120) not found |

  @api @medias @gallery @unique @ok
  Scenario Outline: Retrieves the medias of a specific gallery by a unique ID
    When I send a GET request to "<resource>"
    Then response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message_1>"
    And response should contain "<message_2>"
    And response should contain "<message_3>"

    Examples:
      | resource| status_code | format | message_1 | message_2 | message_3 |
      | /api/media/galleries/1/medias.json | 200 | json | IMG_3587.jpg | IMG_3008.jpg | switzerland_2012-05-19_006.jpg |
      | /api/media/galleries/1/medias.xml  | 200 | xml  | IMG_3587.jpg | IMG_3008.jpg | switzerland_2012-05-19_006.jpg |

  @api @medias @gallery @unique @ko
  Scenario Outline: Unable to retrieve the medias of a specific unexisting gallery by a unique ID
    When I send a GET request to "<resource>"
    Then response code should be <status_code>
    And response should contain "<format>" object
    And response should contain "<message>"

    Examples:
      | resource| status_code | format | message |
      | /api/media/galleries/120/medias.json | 404 | json | Gallery (120) not found |
      | /api/media/galleries/120/medias.xml  | 404 | xml  | Gallery (120) not found |