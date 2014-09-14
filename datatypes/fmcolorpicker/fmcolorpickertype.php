<?php


/**
 * fmColorpickerType class implements the fmColorpicker datatype
 */
class fmColorpickerType extends eZDataType
{
    const DATA_TYPE_STRING = "fmcolorpicker";
    const FMCP_VAR = "_fmcolorpicker_data_text_";
    const FMCP_FIELD = "data_text";

    /**
     * Constructor
     */
    function __construct()
    {
        parent::eZDataType( self::DATA_TYPE_STRING, ezpI18n::tr( "extension/fmColorpicker/datatypes", "fmColorpicker" ), array( "serialize_supported" => true ) );
    }

    /**
     * Sets the default value
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param eZContentObjectVersion $currentVersion
     * @param eZContentObjectAttribute $originalContentObjectAttribute
     */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $data = trim( $originalContentObjectAttribute->attribute( self::FMCP_FIELD ) );
            $contentObjectAttribute->setAttribute( self::FMCP_FIELD, $data );
        }
    }

    /**
     * Hexadecimal Color Code Regular Expression Pattern
     *
     * @param string $data
     *
     * @return bool
     */
    private function validateColor( $data )
    {
        if ( preg_match( "/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/", $data ) !== 1 )
        {
            return false;
        }
        return true;
    }

    /**
     * Validates the input and returns the validity status code
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $contentObjectAttribute
     *
     * @return int
     */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        /** @var eZContentClassAttribute $classAttribute */
        $classAttribute = $contentObjectAttribute->contentClassAttribute();

        if ( $http->hasPostVariable( $base . self::FMCP_VAR . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = trim( $http->postVariable( $base . self::FMCP_VAR . $contentObjectAttribute->attribute( "id" ) ) );

            if ( empty( $data ) )
            {
                if ( !$classAttribute->attribute( "is_information_collector" ) && $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( "kernel/classes/datatypes", "Input required." ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
            else if ( !$this->validateColor( $data ) )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( "extension/fmColorpicker/datatypes", "the color code is not valid." ) );
                return eZInputValidator::STATE_INVALID;
            }
        }
        else if ( !$classAttribute->attribute( "is_information_collector" ) && $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( "kernel/classes/datatypes", "Input required." ) );
            return eZInputValidator::STATE_INVALID;
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Validates the input for collection attribute and returns the validity status code
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $contentObjectAttribute
     *
     * @return int
     */
    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . self::FMCP_VAR . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = trim( $http->postVariable( $base . self::FMCP_VAR . $contentObjectAttribute->attribute( "id" ) ) );

            if ( empty( $data ) )
            {
                if ( $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( "kernel/classes/datatypes", "Input required." ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
            else if ( !$this->validateColor( $data ) )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( "extension/fmColorpicker/datatypes", "the color code is not valid." ) );
                return eZInputValidator::STATE_INVALID;
            }
        }
        else
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( "kernel/classes/datatypes", "Input required." ) );
            return eZInputValidator::STATE_INVALID;
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Fetches the HTTP POST input and stores it in the data instance
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $contentObjectAttribute
     *
     * @return bool
     */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . self::FMCP_VAR . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = trim( $http->postVariable( $base . self::FMCP_VAR . $contentObjectAttribute->attribute( "id" ) ) );
            $contentObjectAttribute->setAttribute( self::FMCP_FIELD, $data );

            return true;
        }

        return false;
    }

    /**
     * Fetches the HTTP POST input and stores it in the data instance
     *
     * @param eZInformationCollection $collection
     * @param eZInformationCollectionAttribute $collectionAttribute
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $contentObjectAttribute
     *
     * @return bool
     */
    function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . self::FMCP_VAR . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = trim( $http->postVariable( $base . self::FMCP_VAR . $contentObjectAttribute->attribute( "id" ) ) );
            $collectionAttribute->setAttribute( self::FMCP_FIELD, $data );

            return true;
        }

        return false;
    }

    /**
     * Does nothing since it uses the data_text field in the content object attribute.
     * See fetchObjectAttributeHTTPInput for the actual storing.
     *
     * @param eZContentObjectAttribute $attribute
     */
    function storeObjectAttribute( $attribute )
    {
    }

    /**
     * Simple string insertion is supported.
     *
     * @return bool
     */
    function isSimpleStringInsertionSupported()
    {
        return true;
    }

    /**
     * @param eZContentObject $object
     * @param eZContentObjectVersion $objectVersion
     * @param string $objectLanguage
     * @param eZContentObjectAttribute $objectAttribute
     * @param string $string
     * @param array $result
     * @return bool
     */
    function insertSimpleString( $object, $objectVersion, $objectLanguage, $objectAttribute, $string, &$result )
    {
        $result = array(
            "errors" => array(),
            "require_storage" => true
        );

        $objectAttribute->setContent( trim( $string ) );
        $objectAttribute->setAttribute( self::FMCP_FIELD, trim( $string ) );

        return true;
    }

    /**
     * Returns the content.
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     *
     * @return string
     */
    function objectAttributeContent( $contentObjectAttribute )
    {
        return trim( $contentObjectAttribute->attribute( self::FMCP_FIELD ) );
    }

    /**
     * Returns the meta data used for storing search indices.
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     *
     * @return string
     */
    function metaData( $contentObjectAttribute )
    {
        return $this->objectAttributeContent( $contentObjectAttribute );
    }

    /**
     * Returns string representation of data for simplified export
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     *
     * @return string
     */
    function toString( $contentObjectAttribute )
    {
        return $this->objectAttributeContent( $contentObjectAttribute );
    }

    /**
     * Imports the data to the attribute
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param string $string
     *
     * @return string
     */
    function fromString( $contentObjectAttribute, $string )
    {
        $contentObjectAttribute->setAttribute( self::FMCP_FIELD, trim( $string ) );
    }

    /**
     * Returns the content of the attribute for use as a title
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param string $name
     *
     * @return string
     */
    function title( $contentObjectAttribute, $name = null )
    {
        return $this->objectAttributeContent( $contentObjectAttribute );
    }

    /**
     * Returns true if attribute has content, false otherwise
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     *
     * @return bool
     */
    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $data = $this->objectAttributeContent( $contentObjectAttribute );
        return !empty( $data );
    }

    /**
     * Returns if the attribute is indexable
     *
     * @return bool
     */
    function isIndexable()
    {
        return true;
    }

    /**
     * Returns if the datatype is information collector
     *
     * @return bool
     */
    function isInformationCollector()
    {
        return true;
    }

    /**
     * Returns the sort key of the attribute
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     *
     * @return string
     */
    function sortKey( $contentObjectAttribute )
    {
        $trans = eZCharTransform::instance();
        return $trans->transformByGroup( trim( $contentObjectAttribute->attribute( self::FMCP_FIELD ) ), "lowercase" );
    }

    /**
     * Returns the sort key type of the attribute
     *
     * @return string
     */
    function sortKeyType()
    {
        return "string";
    }

    /**
     * Returns an eZDiffContent object with the detected changes
     *
     * @param eZContentObjectAttribute $old
     * @param eZContentObjectAttribute $new
     * @param array|bool $options
     *
     * @return eZDiffContent
     */
    function diff( $old, $new, $options = false )
    {
        $diff = new eZDiff();
        $diff->setDiffEngineType( $diff->engineType( "text" ) );
        $diff->initDiffEngine();

        return $diff->diff( $old->content(), $new->content() );
    }

    /**
     * Returns if the content supports batch initialization
     *
     * @return bool
     */
    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }
}

eZDataType::register( fmColorpickerType::DATA_TYPE_STRING, "fmColorpickerType" );
