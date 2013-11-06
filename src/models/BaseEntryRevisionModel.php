<?php
namespace Craft;

craft()->requirePackage(CraftPackage::PublishPro);

/**
 *
 */
class BaseEntryRevisionModel extends EntryModel
{
	/**
	 * @access protected
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array_merge(parent::defineAttributes(), array(
			'creatorId' => AttributeType::Number,
		));
	}

	/**
	 * Sets the revision content.
	 *
	 * @param array $content
	 */
	public function setContentFromRevision($content)
	{
		// Swap the field IDs with handles
		$contentByFieldHandles = array();

		foreach ($content as $fieldId => $value)
		{
			$field = craft()->fields->getFieldById($fieldId);

			if ($field)
			{
				$contentByFieldHandles[$field->handle] = $value;
			}
		}

		// Set the values and prep them
		$this->getContent()->setAttributes($contentByFieldHandles);

		$type = $this->getType();

		if ($type)
		{
			craft()->content->prepElementContentForSave($this, $type->getFieldLayout(), false);
		}
	}

	/**
	 * Returns the draft's creator.
	 *
	 * @return UserModel|null
	 */
	public function getCreator()
	{
		return craft()->users->getUserById($this->creatorId);
	}
}
